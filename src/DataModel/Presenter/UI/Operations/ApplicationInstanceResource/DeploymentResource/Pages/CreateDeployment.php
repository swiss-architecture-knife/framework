<?php

namespace Swark\DataModel\Presenter\UI\Operations\ApplicationInstanceResource\DeploymentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Presenter\UI\Operations\DeploymentResource;

class CreateDeployment extends CreateRecord
{
    protected static string $resource = DeploymentResource::class;
}
