<?php

namespace Swark\DataModel\Presenter\UI\SoftwareArchitecture\ReleaseTrainResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\ReleaseTrainResource;

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
