<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Network\DnsZoneResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\DnsZoneResource;

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
