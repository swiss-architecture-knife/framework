<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\ArchitectureTypeResources\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Presenter\UI\InformationTechnology\ArchitectureTypeResource;

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
