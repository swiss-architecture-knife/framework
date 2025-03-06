<?php

namespace Swark\DataModel\Presenter\UI\Auditing\ActionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Presenter\UI\Auditing\ActionResource;

class CreateAction extends CreateRecord
{
    protected static string $resource = ActionResource::class;
}
