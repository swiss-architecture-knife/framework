<?php

namespace Swark\DataModel\Governance\Infrastructure\UI\TechnologyResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Governance\Infrastructure\UI\TechnologyResource;

class ListTechnologies extends ListRecords
{
    protected static string $resource = TechnologyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
