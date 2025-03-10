<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\SystemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\SystemResource;

class ListSystems extends ListRecords
{
    protected static string $resource = SystemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
