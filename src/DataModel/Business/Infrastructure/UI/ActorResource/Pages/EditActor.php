<?php

namespace Swark\DataModel\Business\Infrastructure\UI\ActorResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Business\Infrastructure\UI\ActorResource;

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
