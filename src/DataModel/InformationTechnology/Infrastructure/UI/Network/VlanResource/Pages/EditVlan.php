<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\VlanResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\VlanResource;

class EditVlan extends EditRecord
{
    protected static string $resource = VlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
