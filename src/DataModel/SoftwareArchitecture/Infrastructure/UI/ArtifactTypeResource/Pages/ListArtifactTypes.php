<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\ArtifactTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\ArtifactTypeResource;

class ListArtifactTypes extends ListRecords
{
    protected static string $resource = ArtifactTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
