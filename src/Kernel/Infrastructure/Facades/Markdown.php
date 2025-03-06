<?php
declare(strict_types=1);

namespace Swark\Kernel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * This facade is used by Cms and DataModel so it belongs to the Kernel
 */
class Markdown extends Facade
{
    const ALIAS = "markdown";

    protected static function getFacadeAccessor(): string
    {
        return static::ALIAS;
    }
}
