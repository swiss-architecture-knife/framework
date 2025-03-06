<?php

namespace Swark\DataModel\Domain\Model\Compliance;

use Swark\Kernel\Domain\Model\EnumToMap;

enum RelevanceType: string
{
    use EnumToMap;

    case NONE = 'none';
    case LOW = 'low';
    case MIDDLE = 'middle';
    case HIGH = 'high';

}
