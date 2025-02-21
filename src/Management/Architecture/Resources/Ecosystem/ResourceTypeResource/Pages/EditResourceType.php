<?php

namespace Swark\Management\Architecture\Resources\Ecosystem\ResourceTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Ecosystem\ResourceTypeResource;

class EditResourceType extends EditRecord
{
    protected static string $resource = ResourceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
