<?php

namespace Swark\DataModel\Presenter\UI\SoftwareArchitecture\ArtifactTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\ArtifactTypeResource;

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
