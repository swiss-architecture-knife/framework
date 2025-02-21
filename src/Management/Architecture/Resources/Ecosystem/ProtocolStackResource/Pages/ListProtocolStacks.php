<?php

namespace Swark\Management\Architecture\Resources\Ecosystem\ProtocolStackResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Ecosystem\ProtocolStackResource;

class ListProtocolStacks extends ListRecords
{
    protected static string $resource = ProtocolStackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
