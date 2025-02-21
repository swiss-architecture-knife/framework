<?php

namespace Swark\Management\Architecture\Resources\Ecosystem\ResourceTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Ecosystem\ResourceTypeResource;

class ListResourceTypes extends ListRecords
{
    protected static string $resource = ResourceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
