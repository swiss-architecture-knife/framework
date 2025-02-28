<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource;

class ListClusters extends ListRecords
{
    protected static string $resource = ClusterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
