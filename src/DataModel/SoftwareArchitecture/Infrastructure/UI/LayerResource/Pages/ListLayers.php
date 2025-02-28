<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\LayerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\LayerResource;

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
