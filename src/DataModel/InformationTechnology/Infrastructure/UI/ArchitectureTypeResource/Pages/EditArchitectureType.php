<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\ArchitectureTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\ArchitectureTypeResource;

class EditArchitectureType extends EditRecord
{
    protected static string $resource = ArchitectureTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
