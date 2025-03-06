<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology;

use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Swark\DataModel\Presenter\UI\Concerns\HasPanelDecorator;
use Swark\DataModel\Presenter\UI\Shared;

class InformationTechnologyPanelProvider extends PanelProvider
{
    use HasPanelDecorator;

    public function panel(Panel $panel): Panel
    {
        return static::decorator($panel
            ->brandName("IT")
            ->id('it')
            ->path('admin/it')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(Shared::ECOSYSTEM)
                    ->icon('heroicon-c-user-group'),
                NavigationGroup::make()
                    ->label(Shared::ENTERPRISE_ARCHITECTURE)
                    ->icon('heroicon-m-square-3-stack-3d'),
                NavigationGroup::make()
                    ->label(Shared::INFRASTRUCTURE)
                    ->icon('heroicon-m-cube-transparent'),
                NavigationGroup::make()
                    ->label(Shared::CLOUD)
                    ->icon('heroicon-m-cloud'),
            ])
        )->decorate(__DIR__, __NAMESPACE__);
    }
}
