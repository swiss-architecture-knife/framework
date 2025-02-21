<?php
declare(strict_types=1);

namespace Swark\Ingester\Model\Configuration;

class OptionValueWithSource
{
    public function __construct(public readonly mixed $value, public readonly string $source)
    {
    }
}
