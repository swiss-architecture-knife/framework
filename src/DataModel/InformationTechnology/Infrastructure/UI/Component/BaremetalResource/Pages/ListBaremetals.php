<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\BaremetalResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\BaremetalResource;

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
