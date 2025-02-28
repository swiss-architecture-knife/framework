<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\DnsZoneResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\DnsZoneResource;

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
