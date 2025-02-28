<?php

namespace Swark\DataModel\Compliance\UI\FindingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Compliance\UI\FindingResource;

class ListFindings extends ListRecords
{
    protected static string $resource = FindingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
