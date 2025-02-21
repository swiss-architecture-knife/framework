<?php

namespace Swark\Management\Architecture\Resources\Software\ArtifactTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Software\ArtifactTypeResource;

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
