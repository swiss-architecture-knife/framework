<?php

namespace Swark\Api\Client\Domain\Host;

use Swark\Api\Client\Aspects\WithNamedType;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;
use Swark\Api\Client\Types\NamedId;

class Host
{
    use WithNamedType;

    public function __construct(
        public readonly Name     $name,
        public readonly NamedId  $operatingSystem,
        public readonly ?Id      $id = null,
        public readonly ?NamedId $baremetal = null,
        public readonly ?NamedId $virtualizer = null,
        public readonly ?NamedId $parentHost = null,
        public readonly ?NamedId $customer = null,
    )
    {
    }

    public function toArray(): array
    {
        return [
                'name' => $this->name->value,
                'operating_system' => $this->operatingSystem->value,
                'id' => $this->id?->value,
                'baremetal' => $this->baremetal?->__toString(),
                'virtualizer' => $this->virtualizer?->__toString(),
                'parent_host' => $this->parentHost?->__toString(),
                'customer' => $this->customer?->__toString(),
            ] + $this->joinNamings();
    }
}
