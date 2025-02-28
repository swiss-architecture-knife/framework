<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\DnsZoneResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\DnsZoneResource;

class ListDnsZones extends ListRecords
{
    protected static string $resource = DnsZoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
