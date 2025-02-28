<?php

namespace Swark\DataModel\Compliance\Infrastructure\UI\RegulationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Compliance\Infrastructure\UI\RegulationResource;

class EditRegulation extends EditRecord
{
    protected static string $resource = RegulationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
