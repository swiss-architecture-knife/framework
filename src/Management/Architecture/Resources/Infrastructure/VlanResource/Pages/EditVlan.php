<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\VlanResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Infrastructure\VlanResource;

class EditVlan extends EditRecord
{
    protected static string $resource = VlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
