<?php
declare(strict_types=1);

namespace Swark\Ingester\Model\Converter;

use Swark\Ingester\Model\Context;
use Swark\Ingester\Model\Relationship\Attribute;

class Converters implements Converter
{
    /**
     * @var Converter[] array
     */
    private array $converters = [];

    public function add(Converter $converter): Converter
    {
        $this->converters[] = $converter;
        return $this;
    }

    public function convert(
        mixed     $value,
        Attribute $attribute,
        array     $attributeMapping = [],
        ?Context  $context = null,
    ): mixed
    {
        $r = $value;

        foreach ($this->converters as $converter) {
            $r = $converter->convert($r, $attribute, $attributeMapping, $context);
        }

        return $r;
    }
}
