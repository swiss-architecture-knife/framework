<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\RuntimeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Infrastructure\RuntimeResource;

class ListRuntimes extends ListRecords
{
    protected static string $resource = RuntimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
