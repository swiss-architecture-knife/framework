<?php

namespace Swark\DataModel\Presenter\UI\SoftwareArchitecture\LayerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\LayerResource;

class ListLayers extends ListRecords
{
    protected static string $resource = LayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
