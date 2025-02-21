<?php

namespace Swark\Management\Architecture\Resources\Software\ArtifactTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Software\ArtifactTypeResource;

class EditArtifactType extends EditRecord
{
    protected static string $resource = ArtifactTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
