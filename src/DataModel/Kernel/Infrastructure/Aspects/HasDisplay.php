<?php

namespace Swark\DataModel\Kernel\Infrastructure\Aspects;

use Illuminate\Support\HtmlString;
use Swark\Content\Infrastructure\Facades\Markdown;

trait HasDisplay
{
    public function display(string $property, ?string $defaultString = ''): HtmlString
    {
        return new HtmlString(Markdown::convert($this->$property ?? $defaultString));
    }
}
