<?php

namespace Swark\Management\Architecture\Resources\Ecosystem\ActorResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Ecosystem\ActorResource;

class EditActor extends EditRecord
{
    protected static string $resource = ActorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
