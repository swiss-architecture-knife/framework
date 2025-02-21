<?php
declare(strict_types=1);

namespace Swark\Ingester\Sink\Uniqueness;

class CompoundIdentifier
{
    private array $context = [];
    private array $identifiers = [];
    private ?string $id = null;

    private array $sources = [];

    public function fromSource(CompoundIdentifiersRepository $compoundIdentifiersRepository)
    {
        $this->sources[] = $compoundIdentifiersRepository;
    }

    /**
     * @return CompoundIdentifiersRepository[]
     */
    public function sources(): array
    {
        return $this->sources;
    }

    public function getContext(string $key): mixed
    {
        return $this->context[$key] ?? null;
    }

    public function setContext(string $key, mixed $value): void
    {
        $this->context[$key] = $value;
    }

    public function add(Identifier $identifier): CompoundIdentifier
    {
        $this->identifiers[] = $identifier;
        return $this;
    }

    public function getRawIds(): array
    {
        $r = [];

        /** @var Identifier $identifier */
        foreach ($this->identifiers as $identifier) {
            $r[$identifier->attribute->name] = $identifier->value;
        }

        return $r;

    }

    public function getMappedIds(): array
    {
        $r = [];

        /** @var Identifier $identifier */
        foreach ($this->identifiers as $identifier) {
            $r[$identifier->attribute->name] = $identifier->value;
        }

        return $r;
    }

    public function getId()
    {
        if ($this->id == null) {
            $values = [];

            foreach ($this->identifiers as $unique) {
                $values[] = $unique->value;
            }

            $this->id = implode(":", $values);
        }

        return $this->id;
    }
}
