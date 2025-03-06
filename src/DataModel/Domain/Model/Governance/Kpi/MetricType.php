<?php

namespace Swark\DataModel\Domain\Model\Governance\Kpi;

use Swark\Kernel\Domain\Model\EnumToMap;

enum MetricType: string
{
    use EnumToMap;

    case BOOLEAN = 'boolean';
    case DECIMAL = 'decimal';
    case PERCENTAGE = 'percentage';
    case TIME_SECONDS = 'time_seconds';
    case TIME_MINUTES = 'time_minutes';
    case TIME_HOURS = 'time_hours';
    case TIME_DAYS = 'time_days';
}
