<?php

namespace Swark\DataModel\Operations\Infrastructure\UI\StageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Operations\Infrastructure\UI\StageResource;

class ListStages extends ListRecords
{
    protected static string $resource = StageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
