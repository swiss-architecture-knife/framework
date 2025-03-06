<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\ArchitectureTypeResources\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Presenter\UI\InformationTechnology\ArchitectureTypeResource;

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
