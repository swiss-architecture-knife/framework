<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Cloud\AvailabilityZoneResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Presenter\UI\InformationTechnology\Cloud\AvailabilityZoneResource;

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
