<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\LayerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\LayerResource;

class EditLayer extends EditRecord
{
    protected static string $resource = LayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
