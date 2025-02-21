<?php

namespace Swark\Api\Client\Domain\Baremetal;

use Swark\Api\Client\Domain\JsonDataResponse;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;

class RegionResponse
{
    public function __construct(
        public Name $name,
        public Id   $id,
    )
    {
    }

    public static function of(JsonDataResponse|array $item): RegionResponse
    {
        return new RegionResponse(
            name: Name::of($item['name']),
            id: Id::of($item['id']),
        );
    }
}
