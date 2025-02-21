<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\NicResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Swark\Management\Architecture\Resources\Infrastructure\NicResource;

class EditNic extends EditRecord
{
    use NestedPage;

    protected static string $resource = NicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
