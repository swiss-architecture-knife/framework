<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Network\DnsRecordResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\DnsRecordResource;

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
