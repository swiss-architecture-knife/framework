<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\IpNetworkResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\IpNetworkResource;

class EditIpNetwork extends EditRecord
{
    protected static string $resource = IpNetworkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
