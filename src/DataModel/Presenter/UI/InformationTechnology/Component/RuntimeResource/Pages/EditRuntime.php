<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Component\RuntimeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Presenter\UI\InformationTechnology\Component\RuntimeResource;

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
