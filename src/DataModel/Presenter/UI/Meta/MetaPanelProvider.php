<?php

namespace Swark\DataModel\Presenter\UI\Meta;

use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Swark\DataModel\Presenter\UI\Concerns\HasPanelDecorator;

class MetaPanelProvider extends PanelProvider
{
    use HasPanelDecorator;

    public function panel(Panel $panel): Panel
    {
        return static::decorator($panel
            ->brandName("Meta")
            ->id('meta')
            ->path('admin/meta')
            ->colors([
                'primary' => Color::Pink,
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
        )->decorate(__DIR__, __NAMESPACE__);
    }
}
