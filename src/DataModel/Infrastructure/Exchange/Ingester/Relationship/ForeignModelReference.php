<?php
declare(strict_types=1);

namespace Swark\DataModel\Infrastructure\Exchange\Ingester\Relationship;

class ForeignModelReference
{
    public function __construct(
        public readonly string $referencedModelAlias,
        public readonly string $referencedModelAttribute,
        public readonly bool   $isOptional = false,
    )
    {
    }
}
