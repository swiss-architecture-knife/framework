<?php

namespace Swark\Management\Architecture\Resources\Deployment\DeploymentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\Management\Architecture\Resources\Deployment\DeploymentResource;

class CreateDeployment extends CreateRecord
{
    protected static string $resource = DeploymentResource::class;
}
