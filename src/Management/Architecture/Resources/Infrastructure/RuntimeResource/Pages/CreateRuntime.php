<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\RuntimeResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\Management\Architecture\Resources\Infrastructure\RuntimeResource;

class CreateRuntime extends CreateRecord
{
    protected static string $resource = RuntimeResource::class;
}
