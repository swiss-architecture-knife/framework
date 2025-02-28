<?php

namespace Swark\DataModel\InformationTechnology\Domain\Model\Component;

enum ExecutorType: string
{
    case HOST = 'host';
    case RUNTIME = 'runtime';
    case DEPLOYMENT = 'deployment';
    case UNKNOWN = 'unknown';

    case CLUSTER = 'cluster';
}
