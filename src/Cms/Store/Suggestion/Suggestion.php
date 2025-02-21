<?php

namespace Swark\Cms\Store\Suggestion;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Swark\Cms\ResourceName;

abstract class Suggestion implements Htmlable
{
    public function __construct(public readonly ResourceName $resourceName)
    {
    }
}
