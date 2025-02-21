<?php

namespace Swark\Api\Client\Domain\Cluster;

use Swark\Api\Client\Aspects\WithNamedType;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;

class Cluster
{
    use WithNamedType;

    public function __construct(
        public readonly Name $name,
        public readonly ?Id  $id = null,
    )
    {
    }

    public function toArray(): array
    {
        return [
                'name' => $this->name->value,
                'id' => $this->id?->value,
            ] + $this->joinNamings();
    }
}
