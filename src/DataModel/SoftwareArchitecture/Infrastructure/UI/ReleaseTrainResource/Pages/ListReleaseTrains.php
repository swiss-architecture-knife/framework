<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\ReleaseTrainResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\ReleaseTrainResource;

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
