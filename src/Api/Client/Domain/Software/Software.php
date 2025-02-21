<?php

namespace Swark\Api\Client\Domain\Software;

use Swark\Api\Client\Aspects\WithNamedType;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;

class Software
{
    use WithNamedType;

    public function __construct(
        public readonly Name  $name,
        public readonly ?Id   $id = null,
        public readonly ?bool $isRuntime = null,
        public readonly ?bool $isOperatingSystem = null,
    )
    {
    }

    private ?Release $release = null;

    public function withRelease(Release $release)
    {
        $this->release = $release;
        return $this;
    }

    public function toArray(): array
    {
        return [
                'name' => $this->name->value,
                'id' => $this->id?->value,
                'is_runtime' => $this->isRuntime,
                'is_operating_system' => $this->isOperatingSystem,
                '_release' => $this->release?->toArray(),
            ] + $this->joinNamings();
    }
}
