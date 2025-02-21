<?php
declare(strict_types=1);

namespace Swark\Ingester\Model\Relationship;

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
