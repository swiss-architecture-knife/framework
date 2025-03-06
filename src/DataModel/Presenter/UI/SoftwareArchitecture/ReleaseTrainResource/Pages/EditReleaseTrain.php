<?php

namespace Swark\DataModel\Presenter\UI\SoftwareArchitecture\ReleaseTrainResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\ReleaseTrainResource;

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
