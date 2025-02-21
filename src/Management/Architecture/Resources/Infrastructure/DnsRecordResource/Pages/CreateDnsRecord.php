<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\DnsRecordResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\Management\Architecture\Resources\Infrastructure\DnsRecordResource;

class CreateDnsRecord extends CreateRecord
{
    protected static string $resource = DnsRecordResource::class;
}
