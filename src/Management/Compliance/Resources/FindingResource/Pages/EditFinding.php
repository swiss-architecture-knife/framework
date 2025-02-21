<?php

namespace Swark\Management\Compliance\Resources\FindingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Compliance\Resources\FindingResource;

class EditFinding extends EditRecord
{
    protected static string $resource = FindingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
