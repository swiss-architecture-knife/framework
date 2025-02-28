<?php

namespace Swark\DataModel\Meta\Infrastructure\UI\ResourceTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Meta\Infrastructure\UI\ResourceTypeResource;

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
