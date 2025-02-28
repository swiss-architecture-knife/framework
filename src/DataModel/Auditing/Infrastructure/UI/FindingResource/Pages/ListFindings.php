<?php

namespace Swark\DataModel\Auditing\Infrastructure\UI\FindingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Auditing\Infrastructure\UI\FindingResource;

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
