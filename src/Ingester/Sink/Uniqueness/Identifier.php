<?php
declare(strict_types=1);

namespace Swark\Ingester\Sink\Uniqueness;

use Swark\Ingester\Model\Relationship\Attribute;

class Identifier
{
    public function __construct(public readonly string    $value,
                                public readonly Attribute $attribute)
    {
    }
}
