<?php

namespace Swark\Content\Domain\Model;

enum ContentType: string
{
    case HTML = 'html';
    case MARKDOWN = 'markdown';
    case BLADE = 'blade';

    case YAML = 'yaml';

    case JSON = 'json';
}
