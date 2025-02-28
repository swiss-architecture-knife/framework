<?php

namespace Swark\DataModel\Operations\Infrastructure\UI\DeploymentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Operations\Infrastructure\UI\DeploymentResource;

class ListDeployments extends ListRecords
{
    protected static string $resource = DeploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
