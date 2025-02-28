<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\AvailabilityZoneResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\AvailabilityZoneResource;

class EditAvailabilityZone extends EditRecord
{
    protected static string $resource = AvailabilityZoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
