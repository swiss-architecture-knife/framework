<?php

namespace Swark\Management\Architecture\Resources\Enterprise\SystemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Enterprise\SystemResource;

class EditSystem extends EditRecord
{
    protected static string $resource = SystemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
