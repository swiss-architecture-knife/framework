<?php

namespace Swark\Api\Client\Domain\Baremetal;

use Swark\Api\Client\Domain\JsonDataResponse;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;

readonly class BaremetalResponse
{
    public function __construct(
        public ?JsonDataResponse         $response,
        public Name  $name,
        public Id                        $id,
        public ?string                   $description,
        public ?ManagedBaremetalResponse $managed = null,
    )
    {
    }

    public static function of(JsonDataResponse|array $item): BaremetalResponse
    {
        return new BaremetalResponse(
            ($item instanceof JsonDataResponse ? $item : null),
            name: Name::of($item['name']),
            id: Id::of($item['id']),
            description: $item['description'] ?? null,
            managed: !empty($item['managed']) ? ManagedBaremetalResponse::of($item['managed']) : null,
        );
    }
}
