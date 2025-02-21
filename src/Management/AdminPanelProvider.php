<?php

namespace Swark\Management;

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
use Swark\Management\Resources\Shared;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE,
            function (array $scopes): View|string {
                // get page name (or resource) and check if it has a help section
                $value = array_values($scopes)[0];
                $clazz = $value;

                if (in_array(HasHelpSection::class, class_uses_recursive($clazz))) {
                    return $clazz::getHelpSection();
                }

                return '';
            },
        );

        /*
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_HEADER_WIDGETS_AFTER,
            function(array $scopes): View {
                dd(get_defined_vars());

                return view('filament.help-section');
            },
        );
        */

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: __DIR__ . '/Architecture/Resources', for: 'Swark\\Management\\Architecture\\Resources')
            ->discoverPages(in: __DIR__ . '/Architecture/Pages', for: 'Swark\\Management\\Architecture\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: __DIR__ . '/Widgets', for: 'Swark\\Management\\Widgets')
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
                NavigationGroup::make()
                    ->label(Shared::DEPLOYMENT)
                    ->icon('heroicon-m-bars-arrow-up'),
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
