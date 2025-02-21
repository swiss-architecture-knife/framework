<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\RuntimeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Infrastructure\RuntimeResource;

class EditRuntime extends EditRecord
{
    protected static string $resource = RuntimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
