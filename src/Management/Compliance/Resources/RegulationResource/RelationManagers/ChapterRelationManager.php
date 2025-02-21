<?php

namespace Swark\Management\Compliance\Resources\RegulationResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Swark\Management\Compliance\Resources\ChapterResource;

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
