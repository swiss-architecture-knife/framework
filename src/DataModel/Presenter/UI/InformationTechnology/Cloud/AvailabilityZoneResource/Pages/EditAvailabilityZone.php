<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Cloud\AvailabilityZoneResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Presenter\UI\InformationTechnology\Cloud\AvailabilityZoneResource;

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
