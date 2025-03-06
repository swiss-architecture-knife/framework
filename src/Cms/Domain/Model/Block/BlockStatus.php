<?php

namespace Swark\Cms\Domain\Model\Block;

enum BlockStatus
{
    case UNINITALIZED;
    case RESOLVED;
    case MISSING;
}
