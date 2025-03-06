<?php

namespace Swark\DataModel\Domain\Model\InformationTechnology\Component;

use Swark\Kernel\Domain\Model\EnumToMap;

enum ClusterMode: string
{
    use EnumToMap;

    case FAILOVER = 'failover';
    case REPLICA = 'replica';
    case LOADBALANCING = 'lb';
}
