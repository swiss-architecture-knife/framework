<?php

namespace Swark\DataModel\Presenter\UI\Compliance;

use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Swark\DataModel\Presenter\UI\Concerns\HasPanelDecorator;

class CompliancePanelProvider extends PanelProvider
{
    use HasPanelDecorator;

    public function panel(Panel $panel): Panel
    {
        return static::decorator($panel
            ->brandName('Compliance')
            ->id('compliance')
            ->path('admin/compliance')
            ->pages([
                Pages\Dashboard::class
            ])
            ->colors([
                'primary' => Color::Rose,
            ])
        )->decorate(__DIR__, __NAMESPACE__);
    }
}
