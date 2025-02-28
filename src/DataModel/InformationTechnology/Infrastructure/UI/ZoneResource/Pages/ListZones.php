<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\ZoneResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\ZoneResource;

class ListZones extends ListRecords
{
    protected static string $resource = ZoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
