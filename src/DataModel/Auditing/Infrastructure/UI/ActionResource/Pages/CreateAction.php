<?php

namespace Swark\DataModel\Auditing\Infrastructure\UI\ActionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Auditing\Infrastructure\UI\ActionResource;

class CreateAction extends CreateRecord
{
    protected static string $resource = ActionResource::class;
}
