<?php

namespace Swark\DataModel\Auditing\Infrastructure\UI\FindingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Auditing\Infrastructure\UI\FindingResource;

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
