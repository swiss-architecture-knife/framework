<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\IpNetworkResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Infrastructure\IpNetworkResource;

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
