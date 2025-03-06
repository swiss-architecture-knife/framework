<?php

namespace Swark\DataModel\Domain\Model\Governance\Kpi;

use Swark\Kernel\Domain\Model\EnumToMap;

enum GoalDirection: string
{
    use EnumToMap;

    case HIGHER = 'higher';
    case LOWER = 'lower';
}
