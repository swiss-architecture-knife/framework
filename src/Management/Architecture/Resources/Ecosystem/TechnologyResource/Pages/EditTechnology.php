<?php

namespace Swark\Management\Architecture\Resources\Ecosystem\TechnologyResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Ecosystem\TechnologyResource;

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
