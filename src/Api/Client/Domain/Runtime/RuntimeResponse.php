<?php

namespace Swark\Api\Client\Domain\Runtime;

use Swark\Api\Client\Domain\JsonDataResponse;
use Swark\Api\Client\Domain\NamedModelRef;
use Swark\Api\Client\Domain\Software\SoftwareResponse;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;

readonly class RuntimeResponse
{
    public function __construct(
        public ?JsonDataResponse $response,
        public Name              $name,
        public Id                $id,
        public NamedModelRef     $host,
        public SoftwareResponse  $software,
    )
    {
    }

    public static function of(JsonDataResponse|array $item): RuntimeResponse
    {
        return new RuntimeResponse(
            ($item instanceof JsonDataResponse ? $item : null),
            name: Name::of($item['name']),
            id: Id::of($item['id']),
            host: NamedModelRef::of($item['host']),
            software: SoftwareResponse::of($item['software'])
        );
    }
}

