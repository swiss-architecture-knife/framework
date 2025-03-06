<?php
declare(strict_types=1);

namespace Swark\DataModel\Infrastructure\Exchange\Ingester\Configuration;

class OptionValueWithSource
{
    public function __construct(public readonly mixed $value, public readonly string $source)
    {
    }
}
