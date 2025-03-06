<?php

namespace Swark\DataModel\Domain\Model\Meta;

use Swark\Kernel\Domain\Model\EnumToMap;

enum KnownMetaType: string
{
    use EnumToMap;

    case BOOLEAN = 'boolean';
    case STRING = 'string';
    case TEXT = 'text';
    case DATE = 'date';
    case INTEGER = 'int';
    case FLOAT = 'float';

    case JSON = 'json';
    case CUSTOM = 'custom';

}
