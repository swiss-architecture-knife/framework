<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\ProtocolStackResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\ProtocolStackResource;

class ListProtocolStacks extends ListRecords
{
    protected static string $resource = ProtocolStackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
