<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\ProtocolStackResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\ProtocolStackResource;

class EditProtocolStack extends EditRecord
{
    protected static string $resource = ProtocolStackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
