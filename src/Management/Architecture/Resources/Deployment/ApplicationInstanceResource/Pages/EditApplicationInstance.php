<?php

namespace Swark\Management\Architecture\Resources\Deployment\ApplicationInstanceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Deployment\ApplicationInstanceResource;

class EditApplicationInstance extends EditRecord
{
    protected static string $resource = ApplicationInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
