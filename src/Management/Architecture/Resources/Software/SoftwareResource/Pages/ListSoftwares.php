<?php

namespace Swark\Management\Architecture\Resources\Software\SoftwareResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Software\SoftwareResource;

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
