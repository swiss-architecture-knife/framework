<?php

namespace Swark\DataModel\Presenter\UI\Concerns;

use Filament\Forms\Form;
use Filament\Panel;
use Swark\DataModel\Presenter\UI\DataModelDecoratorFactory;
use Swark\DataModel\Presenter\UI\PanelDecoratorFactory;

trait HasPanelDecorator
{
    public static function decorator(Panel $panel)
    {
        return app()->make(PanelDecoratorFactory::class)->create($panel);
    }
}
