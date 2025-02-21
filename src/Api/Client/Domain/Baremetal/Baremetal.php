<?php

namespace Swark\Api\Client\Domain\Baremetal;

use Swark\Api\Client\Types\Description;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;
use Swark\Api\Client\Aspects\WithNamedType;

class Baremetal
{
    use WithNamedType;

    public function __construct(
        public readonly Name         $name,
        public readonly ?Description $description = null,
        public readonly ?Id          $id = null,
    )
    {
    }

    private ?Placement $placement = null;

    public function placement(?Placement $placement): Baremetal
    {
        $this->placement = $placement;
        return $this;
    }

    public function toArray(): array
    {
        return [
                'name' => $this->name->value,
                'description' => $this->description?->value,
                'id' => $this->id?->value,
                'placement' => $this->placement?->toArray()
            ] + $this->joinNamings();
    }
}
