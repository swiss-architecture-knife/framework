<?php

namespace Swark\Management\Architecture\Resources\Deployment\DeploymentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Deployment\DeploymentResource;

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
