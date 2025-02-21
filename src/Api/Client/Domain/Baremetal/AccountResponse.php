<?php

namespace Swark\Api\Client\Domain\Baremetal;

use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;
use Swark\Api\Client\Domain\JsonDataResponse;

readonly class AccountResponse
{
    public function __construct(
        public Name $name,
        public Id   $id,
    )
    {
    }

    public static function of(JsonDataResponse|array $item): AccountResponse
    {
        return new AccountResponse(
            name: Name::of($item['name']),
            id: Id::of($item['id']),
        );
    }
}
