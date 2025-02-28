<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\RegionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\RegionResource;

class EditRegion extends EditRecord
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
