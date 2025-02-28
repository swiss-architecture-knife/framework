<?php

namespace Swark\DataModel\Operations\Infrastructure\UI\ApplicationInstanceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Operations\Infrastructure\UI\ApplicationInstanceResource;

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
