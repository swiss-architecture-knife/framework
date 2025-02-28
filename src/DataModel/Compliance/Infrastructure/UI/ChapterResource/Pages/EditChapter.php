<?php

namespace Swark\DataModel\Compliance\Infrastructure\UI\ChapterResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\Compliance\Infrastructure\UI\ChapterResource;

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
