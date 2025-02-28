<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\SoftwareResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\SoftwareResource;

class CreateSoftware extends CreateRecord
{
    protected static string $resource = SoftwareResource::class;
}
