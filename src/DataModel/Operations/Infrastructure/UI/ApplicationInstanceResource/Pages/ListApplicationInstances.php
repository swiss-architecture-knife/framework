<?php

namespace Swark\DataModel\Operations\Infrastructure\UI\ApplicationInstanceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Operations\Infrastructure\UI\ApplicationInstanceResource;

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
