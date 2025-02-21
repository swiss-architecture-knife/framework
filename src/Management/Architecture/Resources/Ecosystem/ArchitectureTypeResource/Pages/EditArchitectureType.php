<?php

namespace Swark\Management\Architecture\Resources\Ecosystem\ArchitectureTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Ecosystem\ArchitectureTypeResource;

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
