<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Component\RuntimeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Presenter\UI\InformationTechnology\Component\RuntimeResource;

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
