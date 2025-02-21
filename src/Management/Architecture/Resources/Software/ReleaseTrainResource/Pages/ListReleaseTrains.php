<?php

namespace Swark\Management\Architecture\Resources\Software\ReleaseTrainResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Software\ReleaseTrainResource;

class ListReleaseTrains extends ListRecords
{
    protected static string $resource = ReleaseTrainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
