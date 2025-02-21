<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\DnsRecordResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Infrastructure\DnsRecordResource;

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
