<?php

namespace Swark\DataModel\Domain\Model\Business;

use Swark\Kernel\Domain\Model\EnumToMap;

enum AssociatedRole: string
{
    use EnumToMap;

    case OWNER = 'owner';
    case MANAGER = 'manager';
    case CUSTOMER = 'customer';
}
