<?php

namespace Swark\DataModel\Kernel\Infrastructure\Aspects;

use Illuminate\View\View;

trait HasHelpSection
{
    public static function getHelpSection(): View {
        throw new \Exception('No help section view defined. Overwrite static::getHelpSection');
    }
}
