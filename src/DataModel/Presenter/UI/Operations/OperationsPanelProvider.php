<?php

namespace Swark\DataModel\Presenter\UI\Operations;

use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Swark\DataModel\Presenter\UI\Concerns\HasPanelDecorator;
use Swark\DataModel\Presenter\UI\Shared;

class OperationsPanelProvider extends PanelProvider
{
    use HasPanelDecorator;

    public function panel(Panel $panel): Panel
    {
        return static::decorator($panel
            ->brandName("Operations")
            ->id('operations')
            ->path('admin/operations')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(Shared::ECOSYSTEM)
                    ->icon('heroicon-c-user-group'),
            ])
        )->decorate(__DIR__, __NAMESPACE__);
    }
}
