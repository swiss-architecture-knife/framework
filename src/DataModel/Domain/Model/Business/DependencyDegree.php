<?php

namespace Swark\DataModel\Domain\Model\Business;

use Swark\Kernel\Domain\Model\EnumToMap;

enum DependencyDegree: string
{
    use EnumToMap;

    case NORMAL = 'normal';
    case HIGH = 'high';
    case VERY_HIGH = 'very_high';
}
