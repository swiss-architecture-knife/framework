<?php

namespace Swark\Management\Compliance\Resources\ControlResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Compliance\Resources\ControlResource;

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
