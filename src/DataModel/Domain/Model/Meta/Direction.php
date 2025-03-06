<?php

namespace Swark\DataModel\Domain\Model\Meta;

use Swark\Kernel\Domain\Model\EnumToMap;

enum Direction: string
{
    use EnumToMap;
    case UNIDIRECTIONAL = 'unidirectional';
    case BIDIRECTIONAL = 'bidirectional';
}
