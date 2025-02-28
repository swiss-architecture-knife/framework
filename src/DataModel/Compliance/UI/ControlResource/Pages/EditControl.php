<?php

namespace Swark\DataModel\Compliance\UI\ControlResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Compliance\UI\ControlResource;

class EditControl extends EditRecord
{
    protected static string $resource = ControlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
