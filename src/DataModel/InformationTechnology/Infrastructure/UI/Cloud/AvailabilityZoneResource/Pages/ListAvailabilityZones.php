<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\AvailabilityZoneResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\AvailabilityZoneResource;

class ListAvailabilityZones extends ListRecords
{
    protected static string $resource = AvailabilityZoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
