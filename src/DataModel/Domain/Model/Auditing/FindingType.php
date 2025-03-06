<?php

namespace Swark\DataModel\Domain\Model\Auditing;

use Swark\Kernel\Domain\Model\EnumToMap;

enum FindingType: string
{
    use EnumToMap;

    case IMPROVEMENT = 'improvement';
    case RISK = 'risk';
    case BUG = 'bug';
}
