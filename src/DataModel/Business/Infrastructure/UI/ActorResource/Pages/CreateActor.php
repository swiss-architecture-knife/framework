<?php

namespace Swark\DataModel\Business\Infrastructure\UI\ActorResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Business\Infrastructure\UI\ActorResource;

class CreateActor extends CreateRecord
{
    protected static string $resource = ActorResource::class;
}
