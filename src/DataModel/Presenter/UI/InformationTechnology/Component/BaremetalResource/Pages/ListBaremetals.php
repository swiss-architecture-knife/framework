<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Component\BaremetalResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Swark\DataModel\Presenter\UI\InformationTechnology\Component\BaremetalResource;

class ListBaremetals extends ListRecords
{
    use NestedPage;

    protected static string $resource = BaremetalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
