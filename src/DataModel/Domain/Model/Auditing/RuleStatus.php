<?php

namespace Swark\DataModel\Domain\Model\Auditing;

use Swark\Kernel\Domain\Model\EnumToMap;

enum RuleStatus: string
{
    use EnumToMap;

    case OK = 'ok';
    case INVALID = 'invalid';
}
