<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\DnsZoneResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Infrastructure\DnsZoneResource;

class EditDnsZone extends EditRecord
{
    protected static string $resource = DnsZoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
