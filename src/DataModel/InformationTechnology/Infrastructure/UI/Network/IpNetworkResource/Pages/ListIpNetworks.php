<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\IpNetworkResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\IpNetworkResource;

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
