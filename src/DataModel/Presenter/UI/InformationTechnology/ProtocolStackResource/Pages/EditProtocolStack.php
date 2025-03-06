<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\ProtocolStackResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Presenter\UI\InformationTechnology\ProtocolStackResource;

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
