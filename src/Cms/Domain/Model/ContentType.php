<?php

namespace Swark\Cms\Domain\Model;

use Swark\Kernel\Domain\Model\EnumToMap;

enum ContentType: string
{
    use EnumToMap;

    case HTML = 'html';
    case MARKDOWN = 'markdown';
    case BLADE = 'blade';

    case YAML = 'yaml';

    case JSON = 'json';
}
