<?php

namespace Swark\Management\Architecture\Resources\Ecosystem\ArchitectureTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Ecosystem\ArchitectureTypeResource;

class ListArchitectureTypes extends ListRecords
{
    protected static string $resource = ArchitectureTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
