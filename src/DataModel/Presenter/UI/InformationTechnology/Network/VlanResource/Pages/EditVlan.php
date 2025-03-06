<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Network\VlanResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\VlanResource;

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
