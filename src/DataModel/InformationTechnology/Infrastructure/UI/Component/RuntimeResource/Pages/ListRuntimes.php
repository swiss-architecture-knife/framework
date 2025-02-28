<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\RuntimeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\RuntimeResource;

class ListRuntimes extends ListRecords
{
    protected static string $resource = RuntimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
