<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\ClusterResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Infrastructure\ClusterResource;

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
