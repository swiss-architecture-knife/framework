<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\ArchitectureTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\ArchitectureTypeResource;

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
