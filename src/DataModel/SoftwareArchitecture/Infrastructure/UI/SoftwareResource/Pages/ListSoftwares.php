<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\SoftwareResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\SoftwareResource;

class ListSoftwares extends ListRecords
{
    protected static string $resource = SoftwareResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
