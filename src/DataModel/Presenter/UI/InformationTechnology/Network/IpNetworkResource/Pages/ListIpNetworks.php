<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Network\IpNetworkResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\IpNetworkResource;

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
