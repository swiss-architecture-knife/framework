<?php
declare(strict_types=1);

namespace Swark\Ingester\Model;

use Swark\Ingester\Model\Configuration\Options;
use Swark\Ingester\Model\Relationship\Attribute;
use Swark\Ingester\Model\Relationship\Attributes;
use Swark\Ingester\Sink\Item;
use Swark\Ingester\Sink\Loader\ItemDataProvider;
use Swark\Ingester\Sink\Uniqueness\CompoundIdentifiersDelegateRepository;
use Swark\Ingester\Sink\Uniqueness\CompoundIdentifiersRepository;

class Context
{
    private array $flags = [];
    private bool $usable = false;
    private ?Attributes $attributes = null;
    private ?Options $options = null;
    private ?CompoundIdentifiersRepository $uniqueItemsProvider = null;

    public function __construct(public readonly string $alias)
    {
    }

    public function uniqueItemsProvider(?CompoundIdentifiersRepository $uniqueItemsProvider = null): CompoundIdentifiersRepository
    {
        if ($uniqueItemsProvider !== null) {
            $this->uniqueItemsProvider = $uniqueItemsProvider;
        }

        if ($this->uniqueItemsProvider === null) {
            $this->uniqueItemsProvider = new CompoundIdentifiersDelegateRepository();
        }

        return $this->uniqueItemsProvider;
    }

    public function flag(StatusFlag $flag): Context
    {
        $this->flags[$flag->name] = $flag;
        return $this;
    }

    public function attributes(?Attributes $attributes = null): ?Attributes
    {
        if ($attributes !== null) {
            $this->attributes = $attributes;
        }

        if ($this->attributes === null) {
            $this->attributes = new Attributes();
        }

        return $this->attributes;
    }

    private ?array $uniqueAttributes = null;

    public function uniqueAttributes(): array
    {
        if ($this->uniqueAttributes === null) {
            $r = [];

            /** @var Attribute $attribute */
            foreach ($this->attributes as $attribute) {
                if ($attribute->isUnique) {
                    $r[] = $attribute;
                }
            }

            $this->uniqueAttributes = $r;
        }

        return $this->uniqueAttributes;
    }

    private ?array $foreignModelReferences = null;

    /**
     * @return Attribute[]
     */
    public function foreignModelReferences(): array
    {
        if ($this->foreignModelReferences === null) {
            $r = [];
            /** @var Attribute $attribute */
            foreach ($this->attributes as $attribute) {
                if ($attribute->foreignModelReference) {
                    $r[] = $attribute;
                }
            }

            $this->foreignModelReferences = $r;
        }

        return $this->foreignModelReferences;
    }

    public function options(?Options $options = null): ?Options
    {
        if ($options !== null) {
            $this->options = $options;
        }

        return $this->options;
    }

    public function unusableBecause(StatusFlag $flag): Context
    {
        $this->flag($flag)->isUsable(false);
        return $this;
    }

    public function hasFlag(StatusFlag $flag): bool
    {
        return isset($this->flags[$flag->name]);
    }

    public function flags(): array
    {
        return array_values($this->flags);
    }

    public function isUsable(?bool $set = null): bool
    {
        if ($set !== null) {
            $this->usable = $set;
        }

        return $this->usable;
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        $r = [];
        $compoundIdentifiers = $this->uniqueItemsProvider()->findCompoundIdentifiers();

        foreach ($compoundIdentifiers as $compoundIdentifier) {
            $mapColumnToRawValue = [];

            foreach ($compoundIdentifier->sources() as $source) {
                if ($source instanceof ItemDataProvider) {
                    $mapColumnToRawValue = $source->upsertItem($compoundIdentifier, $mapColumnToRawValue);
                }
            }

            $r[] = new Item($compoundIdentifier, $mapColumnToRawValue);
        }

        return $r;
    }
}
