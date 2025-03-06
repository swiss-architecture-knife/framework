<?php
declare(strict_types=1);

namespace Swark\DataModel\Infrastructure\Exchange\Ingester\Converter;

use Swark\DataModel\Infrastructure\Exchange\Ingester\Context;
use Swark\DataModel\Infrastructure\Exchange\Ingester\Relationship\Attribute;

class PassthroughConverter implements Converter
{
    public function convert(
        mixed     $value,
        Attribute $attribute,
        array     $attributeMapping = [],
        ?Context  $context = null,
    ): mixed
    {
        return $value;
    }
}
