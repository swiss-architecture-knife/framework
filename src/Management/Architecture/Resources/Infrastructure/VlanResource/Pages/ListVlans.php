<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\VlanResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Infrastructure\VlanResource;

class ListVlans extends ListRecords
{
    protected static string $resource = VlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
