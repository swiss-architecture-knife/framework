<?php

namespace Swark\DataModel\Operations\Infrastructure\UI\StageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Operations\Infrastructure\UI\StageResource;

class CreateStage extends CreateRecord
{
    protected static string $resource = StageResource::class;
}
