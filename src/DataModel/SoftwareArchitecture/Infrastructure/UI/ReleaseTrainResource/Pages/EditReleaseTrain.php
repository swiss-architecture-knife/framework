<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\ReleaseTrainResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\ReleaseTrainResource;

class EditReleaseTrain extends EditRecord
{
    protected static string $resource = ReleaseTrainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
