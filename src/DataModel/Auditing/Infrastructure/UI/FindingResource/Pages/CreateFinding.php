<?php

namespace Swark\DataModel\Auditing\Infrastructure\UI\FindingResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Auditing\Infrastructure\UI\FindingResource;

class CreateFinding extends CreateRecord
{
    protected static string $resource = FindingResource::class;
}
