<?php

namespace Swark\DataModel\Compliance\Infrastructure\UI\ControlResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Compliance\Infrastructure\UI\ControlResource;

class CreateControl extends CreateRecord
{
    protected static string $resource = ControlResource::class;
}
