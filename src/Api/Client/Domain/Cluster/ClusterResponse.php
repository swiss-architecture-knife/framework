<?php

namespace Swark\Api\Client\Domain\Cluster;

use Swark\Api\Client\Domain\JsonDataResponse;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;

readonly class ClusterResponse
{
    public function __construct(
        public ?JsonDataResponse $response,
        public Name              $name,
        public Id                $id,
    )
    {
    }

    public static function of(JsonDataResponse|array $item): ClusterResponse
    {
        return new ClusterResponse(
            ($item instanceof JsonDataResponse ? $item : null),
            name: Name::of($item['name']),
            id: Id::of($item['id']),
        );
    }
}

