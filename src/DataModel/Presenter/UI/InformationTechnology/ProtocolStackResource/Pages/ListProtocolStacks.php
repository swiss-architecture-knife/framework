<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\ProtocolStackResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Presenter\UI\InformationTechnology\ProtocolStackResource;

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
