<?php

namespace Swark\DataModel\Auditing\Infrastructure\UI\ActionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Auditing\Infrastructure\UI\ActionResource;

class ListActions extends ListRecords
{
    protected static string $resource = ActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
