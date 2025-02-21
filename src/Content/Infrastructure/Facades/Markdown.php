<?php
declare(strict_types=1);

namespace Swark\Content\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class Markdown extends Facade
{
    const ALIAS = "markdown";

    protected static function getFacadeAccessor(): string
    {
        return static::ALIAS;
    }
}
