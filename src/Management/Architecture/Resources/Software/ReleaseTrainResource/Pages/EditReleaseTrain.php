<?php

namespace Swark\Management\Architecture\Resources\Software\ReleaseTrainResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Software\ReleaseTrainResource;

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
