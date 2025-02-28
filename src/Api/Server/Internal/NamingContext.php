<?php

namespace Swark\Api\Server\Internal;

use Swark\DataModel\Meta\Domain\Entity\AdditionalNaming;
use Swark\DataModel\Meta\Domain\Entity\NamingType;

class NamingContext
{
    private function __construct(
        public readonly string $modelType,
        public readonly array  $itemNames = [])
    {
    }

    public static function of(string $modelType, null|ItemReference|int|string $id, ?array $customNames = []): NamingContext
    {
        $customNames = $customNames ?? [];
        $itemNames = [];

        if ($id) {
            $itemNames[] = ItemReference::of($id);
        }

        foreach ($customNames as $customName) {
            $itemNames[] = ItemReference::of($customName);
        }


        return new static($modelType, $itemNames);
    }

    public static function ofNamedReference(string $modelType, string|ItemReference $itemReference): NamingContext
    {
        $itemReference = ItemReference::of($itemReference);

        return new static($modelType, [$itemReference]);
    }

    private ?array $mapped = null;

    private function getMapped(): array
    {
        if (!$this->mapped) {
            $this->mapped = [];

            /** @var ItemReference $itemName */
            foreach ($this->itemNames as $itemName) {
                $namingType = null;

                if ($itemName->isCustomScompType()) {
                    $namingType = NamingType::where('scomp_id', $itemName->type)->first();
                    throw_if(!$namingType, "Naming type " . $itemName->type . " does not exist");
                }

                $this->mapped[] = [$namingType, $itemName];
            }
        }

        return $this->mapped;
    }

    private function mappedItems(): \Generator
    {
        foreach ($this->getMapped() as $k) {
            yield $k[0] => $k[1];
        }
    }

    public function resolve(): ?object
    {
        /**
         * @var  NamingType|null $namingTypeOrNull
         * @var ItemReference $itemName
         */
        foreach ($this->mappedItems() as $namingTypeOrNull => $itemName) {
            if (!$itemName->isScompType()) {
                return $this->modelType::where('id', (int)$itemName->idOrName)->firstOrFail();
            }

            // is a scomp type
            if ($itemName->isDefaultScompType()) {
                return $this->modelType::where('scomp_id', $itemName->idOrName)->first();
            }

            $morphClass = (new $this->modelType)->getMorphClass();

            // find configuration items with this models swark type and an external name
            $results = $this->modelType::whereIn('id', function ($query) use ($namingTypeOrNull, $itemName, $morphClass) {
                $query->select('ref_id')->from('configuration_item')
                    ->where('ref_type', $morphClass)
                    ->whereIn('id', function ($query2) use ($namingTypeOrNull, $itemName) {
                    $query2->select('configuration_item_id')->from('configuration_item_naming')
                        ->where('naming_type_id', $namingTypeOrNull->id)
                        ->where('name', $itemName->idOrName);
                });
            })->get();

            throw_if($results->count() > 1, "Multiple items found with the same name {$morphClass}:{$namingTypeOrNull->Id}:{$itemName->idOrName}");

            if ($results->count() == 1) {
                $r = $results->first();

                return $r;
            }
        }

        return null;
    }

    public function attachCustomNamings(object $r)
    {
        /**
         * @var NamingType $namingTypeOrNull
         * @var ItemReference $itemReference
         */
        foreach ($this->mappedItems() as $namingTypeOrNull => $itemReference) {
            if (!$itemReference->isCustomScompType()) {
                continue;
            }

            AdditionalNaming::updateOrCreate([
                'naming_type_id' => $namingTypeOrNull->id,
                'configuration_item_id' => $r->configurationItem->id
            ], [
                'name' => $itemReference->idOrName,
            ]);
        }

        // make dirty
        $this->mapped = null;
    }

}
