<?php
declare(strict_types=1);

namespace Swark\DataModel\Infrastructure\Exchange\Ingester\Converter;

use Swark\DataModel\Infrastructure\Exchange\Ingester\Context;
use Swark\DataModel\Infrastructure\Exchange\Ingester\IngesterException;
use Swark\DataModel\Infrastructure\Exchange\Ingester\Relationship\Attribute;
use Swark\DataModel\Infrastructure\Exchange\Ingester\StatusFlag;

class EnumConverter implements Converter
{
    public function __construct(public readonly array $options = [])
    {
    }

    public function convert(
        mixed     $value,
        Attribute $attribute,
        array     $attributeMapping = [],
        ?Context  $context = null,
    ): mixed
    {
        $r = $value;

        if (isset($this->options) && isset($this->options['enum'])) {
            $enum = $this->options['enum'];
            $targetEnumField = $value;
            if (isset($this->options['map'][$value])) {
                $targetEnumField = $this->options['map'][$value];
            }

            if (!enum_exists($enum) || !method_exists($enum, 'tryFrom')) {
                throw new IngesterException(StatusFlag::INVALID_CONVERTER, "Trying to convert to enum $enum but this class does not exist or has no tryFrom method");
            }

            $r = $enum::tryFrom($targetEnumField);
            if (!$r) {
                foreach ($enum::cases() as $case) {
                    if ($case->name == $targetEnumField) {
                        $r = $case;
                        break;
                    }
                }
            }
        }

        if (!$r && !$attribute->isNullable) {
            throw new IngesterException(StatusFlag::INVALID_DATA_PROVIDED, "Sink attribute " . $context->alias . "::" . $attribute->name . " could not be converted for value '" . $value . "': That enum field does not exist and target property is not nullable");
        }

        return $r;
    }
}
