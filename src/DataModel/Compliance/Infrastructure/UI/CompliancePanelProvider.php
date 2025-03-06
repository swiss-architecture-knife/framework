<?php

namespace Swark\DataModel\Compliance\Infrastructure\UI;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class CompliancePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $rootNamespace = 'Swark\\DataModel\\Compliance\\Infrastructure\\UI';

        return $panel
            ->brandName('Compliance')
            ->id('compliance')
            ->path('admin/compliance')
            ->login()
            ->colors([
                'primary' => Color::Rose,
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
