<?php

namespace Swark\DataModel\Compliance\Infrastructure\UI\RegulationResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Swark\DataModel\Compliance\Infrastructure\UI\ChapterResource;

class ChapterRelationManager extends RelationManager
{
    protected static string $relationship = 'chapters';

    public function form(Form $form): Form
    {
        return ChapterResource::form($form);
    }

    public function table(Table $table): Table
    {
        return ChapterResource::table($table);
    }
}
