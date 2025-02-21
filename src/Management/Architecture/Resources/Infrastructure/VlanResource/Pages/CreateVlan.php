<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\VlanResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\Management\Architecture\Resources\Infrastructure\VlanResource;

class CreateVlan extends CreateRecord
{
    protected static string $resource = VlanResource::class;
}
