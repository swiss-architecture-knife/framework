<?php

namespace Swark\DataModel\Compliance\Infrastructure\UI\RegulationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Compliance\Infrastructure\UI\RegulationResource;

class ListRegulations extends ListRecords
{
    protected static string $resource = RegulationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
