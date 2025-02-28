<?php

namespace Swark\DataModel\Compliance\UI\FindingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Compliance\UI\FindingResource;

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
