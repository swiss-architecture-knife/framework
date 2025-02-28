<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\ZoneResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\ZoneResource;

class EditZone extends EditRecord
{
    protected static string $resource = ZoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
