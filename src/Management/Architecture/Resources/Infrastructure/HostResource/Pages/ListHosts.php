<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\HostResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Infrastructure\HostResource;

class ListHosts extends ListRecords
{
    protected static string $resource = HostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
