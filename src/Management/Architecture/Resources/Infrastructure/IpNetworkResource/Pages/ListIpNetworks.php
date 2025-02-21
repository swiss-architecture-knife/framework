<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\IpNetworkResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Infrastructure\IpNetworkResource;

class ListIpNetworks extends ListRecords
{
    protected static string $resource = IpNetworkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
