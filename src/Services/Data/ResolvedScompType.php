<?php

namespace Swark\Services\Data;

class ResolvedScompType
{
    public function __construct(
        public readonly string $type,
        public readonly string $scompId,
        public readonly int    $internalId)
    {
    }
}
