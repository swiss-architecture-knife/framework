<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\DnsRecordResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\DnsRecordResource;

class EditDnsRecord extends EditRecord
{
    protected static string $resource = DnsRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
