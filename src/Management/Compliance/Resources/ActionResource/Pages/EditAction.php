<?php

namespace Swark\Management\Compliance\Resources\ActionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Compliance\Resources\ActionResource;

class EditAction extends EditRecord
{
    protected static string $resource = ActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
