<?php

namespace Swark\DataModel\Compliance\Infrastructure\UI\ControlResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Compliance\Infrastructure\UI\ControlResource;

class ListControls extends ListRecords
{
    protected static string $resource = ControlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
