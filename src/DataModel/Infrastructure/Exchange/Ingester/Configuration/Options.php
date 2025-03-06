<?php
declare(strict_types=1);

namespace Swark\DataModel\Infrastructure\Exchange\Ingester\Configuration;

class Options
{
    public function __construct(
        public readonly string $modelClazz,
        public readonly array  $dependsOn = [],
        public readonly array  $attributeMappings = [/* column name => ColumnMapping */],
        public readonly array  $loaders = [],
    )
    {
    }
}
