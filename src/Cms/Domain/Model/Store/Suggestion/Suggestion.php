<?php

namespace Swark\Cms\Domain\Model\Store\Suggestion;

use Illuminate\Contracts\Support\Htmlable;
use Swark\Cms\Domain\Model\ResourceName;

abstract class Suggestion implements Htmlable
{
    public function __construct(public readonly ResourceName $resourceName)
    {
    }
}
