<?php

namespace Swark\DataModel\Presenter\UI\Governance;

use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Swark\DataModel\Presenter\UI\Concerns\HasPanelDecorator;

class GovernancePanelProvider extends PanelProvider
{
    use HasPanelDecorator;

    public function panel(Panel $panel): Panel
    {
        return static::decorator($panel
            ->default()
            ->brandName("Governance")
            ->id('governance')
            ->path('admin/governance')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
        )->decorate(__DIR__, __NAMESPACE__);
    }
}
