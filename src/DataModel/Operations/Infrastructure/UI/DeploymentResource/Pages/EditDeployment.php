<?php

namespace Swark\DataModel\Operations\Infrastructure\UI\DeploymentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Operations\Infrastructure\UI\DeploymentResource;

class EditDeployment extends EditRecord
{
    protected static string $resource = DeploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
