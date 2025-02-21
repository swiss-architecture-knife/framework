<?php
declare(strict_types=1);

namespace Swark\Ingester\Model\Converter;

use Swark\Ingester\Model\Context;
use Swark\Ingester\Model\Relationship\Attribute;

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
