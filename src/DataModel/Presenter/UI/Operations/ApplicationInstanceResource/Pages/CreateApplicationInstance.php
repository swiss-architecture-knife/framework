<?php

namespace Swark\DataModel\Presenter\UI\Operations\ApplicationInstanceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Presenter\UI\Operations\ApplicationInstanceResource;

class CreateApplicationInstance extends CreateRecord
{
    protected static string $resource = ApplicationInstanceResource::class;
}
