<?php

namespace Swark\DataModel\Infrastructure\Aspects;

use Illuminate\Support\HtmlString;
use Swark\Kernel\Infrastructure\Facades\Markdown;

trait HasDisplay
{
    public function display(string $property, ?string $defaultString = ''): HtmlString
    {
        return new HtmlString(Markdown::convert($this->$property ?? $defaultString));
    }
}
