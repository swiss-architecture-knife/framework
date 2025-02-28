<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource;

class EditCluster extends EditRecord
{
    protected static string $resource = ClusterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
