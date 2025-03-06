<?php

namespace Swark\DataModel\Presenter\UI\SoftwareArchitecture;

use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Swark\DataModel\Presenter\UI\Concerns\HasPanelDecorator;
use Swark\DataModel\Presenter\UI\Shared;

class SoftwareArchitecturePanelProvider extends PanelProvider
{
    use HasPanelDecorator;

    public function panel(Panel $panel): Panel
    {
        return static::decorator($panel
            ->brandName("Software architecture")
            ->id('software-architecture')
            ->path('admin/software-architecture')
            ->colors([
                'primary' => Color::Lime,
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(Shared::ECOSYSTEM)
                    ->icon('heroicon-c-user-group'),
                NavigationGroup::make()
                    ->label(Shared::SOFTWARE)
                    ->icon('heroicon-m-bars-3'),
                NavigationGroup::make()
                    ->label(Shared::ENTERPRISE_ARCHITECTURE)
                    ->icon('heroicon-m-square-3-stack-3d'),
            ])
        )->decorate(__DIR__, __NAMESPACE__);

    }
}
