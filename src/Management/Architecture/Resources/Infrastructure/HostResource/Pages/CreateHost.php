<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\HostResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\Management\Architecture\Resources\Infrastructure\HostResource;

class CreateHost extends CreateRecord
{
    protected static string $resource = HostResource::class;
}
