<?php

namespace Swark\DataModel\Presenter\UI\Operations\ApplicationInstanceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Presenter\UI\Operations\ApplicationInstanceResource;

class ListApplicationInstances extends ListRecords
{
    protected static string $resource = ApplicationInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
