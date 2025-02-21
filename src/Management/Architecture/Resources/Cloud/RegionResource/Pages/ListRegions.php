<?php

namespace Swark\Management\Architecture\Resources\Cloud\RegionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Cloud\RegionResource;

class ListRegions extends ListRecords
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
