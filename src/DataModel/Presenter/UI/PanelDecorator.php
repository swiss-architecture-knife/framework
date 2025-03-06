<?php

namespace Swark\DataModel\Presenter\UI;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class PanelDecorator
{
    public function __construct(
        public readonly Panel $panel)
    {
    }

    public function withNamespace(string $rootDirectory, string $useNamespace, array $pages = []): PanelDecorator
    {
        $this->panel
            ->discoverResources(in: $rootDirectory, for: $useNamespace)
            ->discoverPages(in: $rootDirectory, for: $useNamespace)
            ->discoverWidgets(in: $rootDirectory . '/Widgets', for: $useNamespace)
            ->pages($pages);

        return $this;
    }

    public function withWidgets(): PanelDecorator
    {
        $this->panel->widgets([
            AccountWidget::class,
            FilamentInfoWidget::class,
        ]);

        return $this;
    }

    public function withSecurity(): PanelDecorator
    {
        $this->panel
            ->login()
            ->authMiddleware([
                Authenticate::class,
            ]);
        return $this;
    }

    public function withTransactions(): PanelDecorator
    {
        $this->panel->databaseTransactions(true);
        return $this;
    }

    public function withNavigation(): PanelDecorator
    {
        $this->panel->topNavigation();
        return $this;
    }

    public function withMiddleware(): PanelDecorator
    {
        $this->panel->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ]);

        return $this;
    }

    public function decorate(
        string $rootDirectory,
        string $useNamespace,
        array  $pages = [],
    ): Panel
    {
        $this->withNamespace($rootDirectory, $useNamespace, $pages)
            ->withWidgets()
            ->withTransactions()
            ->withNavigation()
            ->withMiddleware()
            ->withSecurity();

        return $this->panel;
    }
}
