<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\DnsZoneResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Infrastructure\DnsZoneResource;

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
