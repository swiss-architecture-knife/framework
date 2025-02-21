<?php

namespace Swark\Management\Architecture\Resources\Deployment\ApplicationInstanceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Deployment\ApplicationInstanceResource;

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
