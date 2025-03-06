<?php

namespace Swark\DataModel\Presenter\UI\Auditing\FindingResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Presenter\UI\Auditing\FindingResource;

class CreateFinding extends CreateRecord
{
    protected static string $resource = FindingResource::class;
}
