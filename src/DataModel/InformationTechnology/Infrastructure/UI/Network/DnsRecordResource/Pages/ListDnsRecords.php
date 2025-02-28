<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\DnsRecordResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\DnsRecordResource;

class ListDnsRecords extends ListRecords
{
    protected static string $resource = DnsRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
