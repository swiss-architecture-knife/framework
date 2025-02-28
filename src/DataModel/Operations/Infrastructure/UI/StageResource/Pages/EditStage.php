<?php

namespace Swark\DataModel\Operations\Infrastructure\UI\StageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Operations\Infrastructure\UI\StageResource;

class EditStage extends EditRecord
{
    protected static string $resource = StageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
