<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\RuntimeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\RuntimeResource;

class EditRuntime extends EditRecord
{
    protected static string $resource = RuntimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
