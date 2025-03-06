<?php

namespace Swark\Cms\Domain\Model\Exchange;

enum ContentImportStatus
{
    case SUCCESS;
    case INFO;
    case WARNING;
    case ERROR;
}
