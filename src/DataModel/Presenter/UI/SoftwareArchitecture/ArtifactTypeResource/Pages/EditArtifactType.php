<?php

namespace Swark\DataModel\Presenter\UI\SoftwareArchitecture\ArtifactTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\ArtifactTypeResource;

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
