<?php

namespace Swark\Cms\Block;

enum BlockStatus
{
    case UNINITALIZED;
    case RESOLVED;
    case MISSING;
}
