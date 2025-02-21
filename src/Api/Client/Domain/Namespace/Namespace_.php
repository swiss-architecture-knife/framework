<?php

namespace Swark\Api\Client\Domain\Namespace;

use Swark\Api\Client\Aspects\WithNamedType;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;
use Swark\Api\Client\Types\NamedId;

class Namespace_
{
    use WithNamedType;

    public function __construct(
        public readonly Name    $name,
        public readonly NamedId $cluster,
        public readonly ?Id     $id = null,
    )
    {
    }

    public function toArray(): array
    {
        return [
                'name' => $this->name->value,
                'id' => $this->id?->value,
                'cluster' => $this->cluster->__toString(),
            ] + $this->joinNamings();
    }
}
