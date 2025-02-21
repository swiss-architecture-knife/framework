<?php

namespace Swark\Cms\Store\Suggestion;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Swark\Cms\ResourceName;

class FilesystemSuggestion extends Suggestion
{
    public function __construct(
        public readonly ResourceName $resourceName,
        public readonly string       $basePath,
        public readonly array        $extensions)
    {

    }

    public function toHtml()
    {
        $suggestions = implode(',', $this->extensions);
        $basePath = $this->basePath;

        return <<<HTML
<strong>In fileystem:</strong> Create a file <code>{$basePath}</code> with one of the extensions <code>{$suggestions}</code>.
HTML;


    }
}
