<?php

namespace Swark\DataModel\Operations\Infrastructure\UI\DeploymentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Operations\Infrastructure\UI\DeploymentResource;

class CreateDeployment extends CreateRecord
{
    protected static string $resource = DeploymentResource::class;
}
