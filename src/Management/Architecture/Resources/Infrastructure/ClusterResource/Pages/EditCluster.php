<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\ClusterResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Infrastructure\ClusterResource;

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
