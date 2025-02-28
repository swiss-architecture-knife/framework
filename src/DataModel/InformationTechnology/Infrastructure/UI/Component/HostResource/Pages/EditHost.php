<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\HostResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\HostResource;

class EditHost extends EditRecord
{
    protected static string $resource = HostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
