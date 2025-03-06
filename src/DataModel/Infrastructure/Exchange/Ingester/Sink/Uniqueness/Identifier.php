<?php
declare(strict_types=1);

namespace Swark\DataModel\Infrastructure\Exchange\Ingester\Sink\Uniqueness;

use Swark\DataModel\Infrastructure\Exchange\Ingester\Relationship\Attribute;

class Identifier
{
    public function __construct(public readonly string    $value,
                                public readonly Attribute $attribute)
    {
    }
}
