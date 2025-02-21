<?php

namespace Swark\Api\Client\Domain\Host;

use Swark\Api\Client\Domain\Baremetal\BaremetalResponse;
use Swark\Api\Client\Domain\JsonDataResponse;
use Swark\Api\Client\Domain\Software\SoftwareResponse;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;

readonly class HostResponse
{
    public function __construct(
        public ?JsonDataResponse $response,
        public Name              $name,
        public Id                $id,

        public BaremetalResponse $baremetal,
        public SoftwareResponse  $operatingSystem,
        public ?SoftwareResponse $virtualizer,
        public ?HostResponse     $parentHost,
    )
    {
    }

    public static function of(JsonDataResponse|array $item): HostResponse
    {
        return new HostResponse(
            ($item instanceof JsonDataResponse ? $item : null),
            name: Name::of($item['name']),
            id: Id::of($item['id']),
            baremetal: !empty($item['baremetal']) ? BaremetalResponse::of($item['baremetal']) : null,
            operatingSystem: !empty($item['operating_system']) ? SoftwareResponse::of($item['operating_system']) : null,
            virtualizer: !empty($item['virtualizer']) ? SoftwareResponse::of($item['virtualizer']) : null,
            parentHost: !empty($item['parent']) ? HostResponse::of($item['parent']) : null,
        );
    }
}

