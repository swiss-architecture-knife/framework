<?php
declare(strict_types=1);

namespace Swark\Ingester\Model\Relationship;

use IteratorAggregate;
use Swark\Ingester\Sink\Structure\Column;
use Traversable;

class Attributes implements IteratorAggregate
{
    private ?Column $updatedAt = null;

    private ?Column $deletedAt = null;

    private array $attributes = [];

    public function add(Attribute $attribute): Attributes
    {
        if (!$this->get($attribute->name)) {
            $this->attributes[] = $attribute;
        }

        return $this;
    }

    public function get(string $attributeName): ?Attribute
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->name == $attributeName) {
                return $attribute;
            }
        }

        return null;
    }

    public function updatedAt(?Column $updatedAt = null): ?Column
    {
        if ($updatedAt !== null) {
            $this->updatedAt = $updatedAt;
        }

        return $this->updatedAt;
    }

    public function deletedAt(?Column $deletedAt = null): ?Column
    {
        if ($deletedAt !== null) {
            $this->deletedAt = $deletedAt;
        }

        return $this->deletedAt;
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->attributes);
    }
}
