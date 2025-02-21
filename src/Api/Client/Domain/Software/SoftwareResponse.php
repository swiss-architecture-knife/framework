<?php

namespace Swark\Api\Client\Domain\Software;

use Swark\Api\Client\Domain\JsonDataResponse;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;

readonly class SoftwareResponse
{
    public function __construct(
        public Name  $name,
        public Id    $id,
        public ?bool $isOperatingSystem = null,
        public ?bool $isRuntime = null,
        public array $releases = [],)
    {
    }

    public static function of(JsonDataResponse|array $item): SoftwareResponse
    {
        return new SoftwareResponse(
            name: Name::of($item['name']),
            id: Id::of($item['id']),
            isOperatingSystem: $item['is_operating_system'] ?? null,
            isRuntime: $item['is_runtime'] ?? null,
            releases: collect($item['releases'])->map(fn($item) => ReleaseResponse::of($item))->toArray(),
        );
    }

    public function favoriteRelease(): ?ReleaseResponse
    {
        $r = null;

        /** @var ReleaseResponse $release */
        foreach ($this->releases as $release) {
            if ($release->isAny) {
                $r = $release;
                break;
            }

            if ($release->isLatest) {
                $r = $release;
                break;
            }

            $r = $release;
        }

        return $r;
    }
}
