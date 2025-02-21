<?php

namespace Swark\Management\Architecture\Resources\Software\Architecture\LayerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Software\Architecture\LayerResource;

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
