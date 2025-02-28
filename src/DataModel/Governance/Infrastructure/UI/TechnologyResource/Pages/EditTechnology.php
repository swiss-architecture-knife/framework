<?php

namespace Swark\DataModel\Governance\Infrastructure\UI\TechnologyResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Governance\Infrastructure\UI\TechnologyResource;

class EditTechnology extends EditRecord
{
    protected static string $resource = TechnologyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
