<?php

namespace Swark\Management\Architecture\Resources\Deployment\StageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\Management\Architecture\Resources\Deployment\StageResource;

class CreateStage extends CreateRecord
{
    protected static string $resource = StageResource::class;
}
