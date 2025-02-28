<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\HostResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\HostResource;

class ListHosts extends ListRecords
{
    protected static string $resource = HostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
