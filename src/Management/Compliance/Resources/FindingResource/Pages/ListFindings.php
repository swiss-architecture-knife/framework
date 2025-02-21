<?php

namespace Swark\Management\Compliance\Resources\FindingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Compliance\Resources\FindingResource;

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
