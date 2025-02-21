<?php

namespace Swark\Management\Compliance\Resources\ChapterResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Compliance\Resources\ChapterResource;

class EditChapter extends EditRecord
{
    protected static string $resource = ChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
