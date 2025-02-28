<?php

namespace Swark\DataModel\Operations\Infrastructure\UI\ApplicationInstanceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Operations\Infrastructure\UI\ApplicationInstanceResource;

class CreateApplicationInstance extends CreateRecord
{
    protected static string $resource = ApplicationInstanceResource::class;
}
