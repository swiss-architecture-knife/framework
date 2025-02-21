<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\HostResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Infrastructure\HostResource;

class EditHost extends EditRecord
{
    protected static string $resource = HostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
