<?php

namespace Swark\DataModel\Domain\Model\SoftwareArchitecture;

use Swark\Kernel\Domain\Model\EnumToMap;

enum UsageType: string
{
    use EnumToMap;

    case CONSOLE = 'console';
    case WEBAPP = 'webapp';
    case CLIENT = 'client';
    case SERVER = 'server';
    case MOBILE = 'mobile';
}
