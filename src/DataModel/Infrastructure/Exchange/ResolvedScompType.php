<?php

namespace Swark\DataModel\Infrastructure\Exchange;

class ResolvedScompType
{
    public function __construct(
        public readonly string $type,
        public readonly string $scompId,
        public readonly int    $internalId)
    {
    }
}
