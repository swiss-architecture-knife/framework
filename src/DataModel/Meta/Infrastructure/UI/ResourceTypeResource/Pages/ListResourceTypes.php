<?php

namespace Swark\DataModel\Meta\Infrastructure\UI\ResourceTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Meta\Infrastructure\UI\ResourceTypeResource;

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
