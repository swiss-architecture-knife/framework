<?php

namespace Swark\Api\Client\Domain\Baremetal;

use Swark\Api\Client\Types\NamedId;

readonly class Placement
{
    public function __construct(
        public NamedId $region,
        public NamedId $availabilityZone,
        public NamedId $accountId,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'account' => $this->accountId->__toString(),
            'region' => $this->region->__toString(),
            'availability_zone' => $this->availabilityZone->__toString(),
        ];
    }
}
