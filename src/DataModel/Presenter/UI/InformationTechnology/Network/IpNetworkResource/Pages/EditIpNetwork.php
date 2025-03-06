<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Network\IpNetworkResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\IpNetworkResource;

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
