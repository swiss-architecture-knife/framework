<?php

namespace Swark\DataModel\Presenter\UI\Operations\ApplicationInstanceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Presenter\UI\Operations\ApplicationInstanceResource;

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
