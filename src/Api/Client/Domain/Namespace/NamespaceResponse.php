<?php

namespace Swark\Api\Client\Domain\Namespace;

use Swark\Api\Client\Domain\Cluster\ClusterResponse;
use Swark\Api\Client\Domain\JsonDataResponse;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;

readonly class NamespaceResponse
{
    public function __construct(
        public ?JsonDataResponse        $response,
        public Name $name,
        public Id                       $id,
        public ClusterResponse          $cluster,
    )
    {
    }

    public static function of(JsonDataResponse|array $item): NamespaceResponse
    {
        return new NamespaceResponse(
            ($item instanceof JsonDataResponse ? $item : null),
            name: Name::of($item['name']),
            id: Id::of($item['id']),
            cluster: ClusterResponse::of($item['cluster'])
        );
    }
}

