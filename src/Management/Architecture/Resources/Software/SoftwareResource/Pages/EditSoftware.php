<?php

namespace Swark\Management\Architecture\Resources\Software\SoftwareResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Software\SoftwareResource;

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
