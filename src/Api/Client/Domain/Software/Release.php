<?php

namespace Swark\Api\Client\Domain\Software;

use Swark\Api\Client\Types\Name;

readonly class Release
{
    public function __construct(
        public Name $version,
        public ?bool                        $isLatest = null,
        public ?bool                        $isAny = null,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'version' => $this->version->value,
            'is_latest' => $this->isLatest,
            'is_any' => $this->isAny,
        ];
    }
}
