<?php

namespace Swark\DataModel\Presenter\UI\Auditing;

use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Swark\DataModel\Presenter\UI\Concerns\HasPanelDecorator;

class AuditingPanelProvider extends PanelProvider
{
    use HasPanelDecorator;

    public function panel(Panel $panel): Panel
    {
        return static::decorator($panel
            ->default()
            ->brandName("Auditing")
            ->id('auditing')
            ->path('admin/auditing')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->colors([
                'primary' => Color::Violet,
            ])
        )->decorate(__DIR__, __NAMESPACE__);
    }
}
