<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\DnsRecordResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Infrastructure\DnsRecordResource;

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
