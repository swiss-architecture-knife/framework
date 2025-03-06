<?php

namespace Swark\DataModel\Presenter\UI\Meta\ResourceTypeResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Presenter\UI\Meta\ResourceTypeResource;

class CreateResourceType extends CreateRecord
{
    protected static string $resource = ResourceTypeResource::class;
}
