<?php

namespace Swark\DataModel\Kernel\Infrastructure\UI;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\View\View;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasHelpSection;

class FilamentHelper {
    public static function registerDefaultRenderHooks() {
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
    }
}
