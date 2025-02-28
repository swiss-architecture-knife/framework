<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\RegionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\RegionResource;

class ListRegions extends ListRecords
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
