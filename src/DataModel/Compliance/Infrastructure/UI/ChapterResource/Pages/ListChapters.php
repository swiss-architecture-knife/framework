<?php

namespace Swark\DataModel\Compliance\Infrastructure\UI\ChapterResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Compliance\Infrastructure\UI\ChapterResource;

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
