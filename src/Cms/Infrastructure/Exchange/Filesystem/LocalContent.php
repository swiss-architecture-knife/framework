<?php

namespace Swark\Cms\Infrastructure\Exchange\Filesystem;

readonly class LocalContent
{
    public function __construct(
        public string $scompId,
        public string $content,
        public string $targetContentType,
    )
    {
    }
}
