<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\DnsZoneResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\Management\Architecture\Resources\Infrastructure\DnsZoneResource;

class CreateDnsZone extends CreateRecord
{
    protected static string $resource = DnsZoneResource::class;
}
