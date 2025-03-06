<?php

namespace Swark\DataModel\Presenter\UI\SoftwareArchitecture\SoftwareResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\SoftwareResource;

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
