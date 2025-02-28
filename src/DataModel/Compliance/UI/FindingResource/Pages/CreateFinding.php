<?php

namespace Swark\DataModel\Compliance\UI\FindingResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Compliance\UI\FindingResource;

class CreateFinding extends CreateRecord
{
    protected static string $resource = FindingResource::class;
}
