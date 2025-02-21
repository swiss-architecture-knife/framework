<?php

namespace Swark\Api\Client\Domain\Software;

use Swark\Api\Client\Domain\JsonDataResponse;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;

readonly class ReleaseResponse
{
    public function __construct(public Name $name,
                                public ?Id  $id,
                                public bool $isLatest,
                                public bool $isAny)
    {
    }

    public static function of(JsonDataResponse|array $item)
    {
        return new static(
            name: Name::of($item['name']),
            id: $item['id'] ? Id::of($item['id']) : null,
            isLatest: $item['is_latest'],
            isAny: $item['is_any']
        );
    }
}
