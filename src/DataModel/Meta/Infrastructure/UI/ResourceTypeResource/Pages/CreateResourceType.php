<?php

namespace Swark\DataModel\Meta\Infrastructure\UI\ResourceTypeResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Meta\Infrastructure\UI\ResourceTypeResource;

class CreateResourceType extends CreateRecord
{
    protected static string $resource = ResourceTypeResource::class;
}
