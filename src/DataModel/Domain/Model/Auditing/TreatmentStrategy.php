<?php

namespace Swark\DataModel\Domain\Model\Auditing;

use Swark\Kernel\Domain\Model\EnumToMap;

enum TreatmentStrategy: string
{
    use EnumToMap;
    case ACCEPT = 'accept';
    case FIX = 'fix';
}
