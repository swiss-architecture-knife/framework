<?php

namespace Swark\DataModel\Auditing\Infrastructure\UI\ActionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Auditing\Infrastructure\UI\ActionResource;

class EditAction extends EditRecord
{
    protected static string $resource = ActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
