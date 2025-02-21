<?php

namespace Swark\Api\Client\Domain\Baremetal;

use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;
use Swark\Api\Client\Domain\JsonDataResponse;

readonly class AvailabilityZoneResponse
{
    public function __construct(
        public Name $name,
        public Id   $id,
    )
    {
    }

    public static function of(JsonDataResponse|array $item): AvailabilityZoneResponse
    {
        return new AvailabilityZoneResponse(
            name: Name::of($item['name']),
            id: Id::of($item['id']),
        );
    }
}
