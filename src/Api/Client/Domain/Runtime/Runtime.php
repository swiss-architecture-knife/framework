<?php

namespace Swark\Api\Client\Domain\Runtime;

use Swark\Api\Client\Aspects\WithNamedType;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;
use Swark\Api\Client\Types\NamedId;

class Runtime
{
    use WithNamedType;

    public function __construct(

        public Name    $name,
        public NamedId $host,
        public NamedId $release,
        public ?Id     $id = null,
    )
    {
    }

    public function toArray(): array
    {
        return [
                'name' => $this->name->value,
                'host' => $this->host->value,
                'release' => $this->release->value,
                'id' => $this->id?->value,
            ] + $this->joinNamings();
    }
}
