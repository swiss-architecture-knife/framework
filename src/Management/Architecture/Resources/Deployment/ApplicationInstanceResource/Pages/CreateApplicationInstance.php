<?php

namespace Swark\Management\Architecture\Resources\Deployment\ApplicationInstanceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\Management\Architecture\Resources\Deployment\ApplicationInstanceResource;

class CreateApplicationInstance extends CreateRecord
{
    protected static string $resource = ApplicationInstanceResource::class;
}
