<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\ArtifactTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\ArtifactTypeResource;

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
