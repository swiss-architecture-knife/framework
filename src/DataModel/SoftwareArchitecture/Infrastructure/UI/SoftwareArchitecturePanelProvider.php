<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\View\View;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasHelpSection;
use Swark\DataModel\Kernel\Infrastructure\UI\FilamentHelper;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class SoftwareArchitecturePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $rootNamespace = 'Swark\\DataModel\\SoftwareArchitecture\\Infrastructure\\UI';

        return $panel
            ->default()
            ->brandName("Software architecture")
            ->id('software-architecture')
            ->path('admin/software-architecture')
            ->login()
            ->colors([
                'primary' => Color::Lime,
            ])
            ->discoverResources(in: __DIR__, for: $rootNamespace)
            ->discoverPages(in: __DIR__, for: $rootNamespace)
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: __DIR__ . '/Widgets', for: $rootNamespace)
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->topNavigation()
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
                NavigationGroup::make()
                    ->label(Shared::INFRASTRUCTURE)
                    ->icon('heroicon-m-cube-transparent'),
                NavigationGroup::make()
                    ->label(Shared::CLOUD)
                    ->icon('heroicon-m-cloud'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
