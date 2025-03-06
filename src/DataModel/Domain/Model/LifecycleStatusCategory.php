<?php

namespace Swark\DataModel\Domain\Model;

use Swark\Kernel\Domain\Model\EnumToMap;

enum LifecycleStatusCategory: string
{
    use EnumToMap;
    case BEGIN = 'begin';
    case BORN = 'born';
    case END = 'end';
}
