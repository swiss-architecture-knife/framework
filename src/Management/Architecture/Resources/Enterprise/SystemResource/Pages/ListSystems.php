<?php

namespace Swark\Management\Architecture\Resources\Enterprise\SystemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Enterprise\SystemResource;

class ListSystems extends ListRecords
{
    protected static string $resource = SystemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
