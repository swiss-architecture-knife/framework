<?php

namespace Swark\Management\Architecture\Resources\Software\Architecture\LayerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Software\Architecture\LayerResource;

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
