<?php

namespace Swark\DataModel\Domain\Model\Auditing;

use Swark\Kernel\Domain\Model\EnumToMap;

enum Status: string
{
    use EnumToMap;

    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case IN_REVIEW = 'in_review';
    case DONE = 'done';
}
