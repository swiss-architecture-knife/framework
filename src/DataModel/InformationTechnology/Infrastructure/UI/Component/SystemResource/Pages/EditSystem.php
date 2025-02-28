<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\SystemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\SystemResource;

class EditSystem extends EditRecord
{
    protected static string $resource = SystemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
