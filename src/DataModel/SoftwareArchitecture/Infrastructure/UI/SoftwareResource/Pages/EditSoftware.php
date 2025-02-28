<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\SoftwareResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\SoftwareResource;

class EditSoftware extends EditRecord
{
    protected static string $resource = SoftwareResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
