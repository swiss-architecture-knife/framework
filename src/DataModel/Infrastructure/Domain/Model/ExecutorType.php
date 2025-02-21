<?php

namespace Swark\DataModel\Infrastructure\Domain\Model;

enum ExecutorType: string
{
    case HOST = 'host';
    case RUNTIME = 'runtime';
    case DEPLOYMENT = 'deployment';
    case UNKNOWN = 'unknown';

    case CLUSTER = 'cluster';
}
