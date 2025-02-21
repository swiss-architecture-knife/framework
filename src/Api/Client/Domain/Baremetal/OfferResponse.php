<?php

namespace Swark\Api\Client\Domain\Baremetal;

use Swark\Api\Client\Domain\JsonDataResponse;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;

readonly class OfferResponse
{
    public function __construct(
        public Name $name,
        public Id   $id,
    )
    {
    }

    public static function of(JsonDataResponse|array $item): OfferResponse
    {
        return new OfferResponse(
            name: Name::of($item['name']),
            id: Id::of($item['id']),
        );
    }
}
