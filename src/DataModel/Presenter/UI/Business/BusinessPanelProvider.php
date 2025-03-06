<?php

namespace Swark\DataModel\Presenter\UI\Business;

use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Swark\DataModel\Presenter\UI\Concerns\HasPanelDecorator;

class BusinessPanelProvider extends PanelProvider
{
    use HasPanelDecorator;

    public function panel(Panel $panel): Panel
    {
        return static::decorator($panel
            ->brandName("Business")
            ->id('business')
            ->path('admin/business')
            ->colors([
                'primary' => Color::Fuchsia,
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
        )->decorate(__DIR__, __NAMESPACE__);
    }
}
