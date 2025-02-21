<?php

namespace Swark\Management\Compliance\Resources\ChapterResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Compliance\Resources\ChapterResource;

class ListChapters extends ListRecords
{
    protected static string $resource = ChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
