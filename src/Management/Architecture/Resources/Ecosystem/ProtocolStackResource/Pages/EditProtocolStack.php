<?php

namespace Swark\Management\Architecture\Resources\Ecosystem\ProtocolStackResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Ecosystem\ProtocolStackResource;

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
