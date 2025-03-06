<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Network\VlanResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\VlanResource;

class ListVlans extends ListRecords
{
    protected static string $resource = VlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
