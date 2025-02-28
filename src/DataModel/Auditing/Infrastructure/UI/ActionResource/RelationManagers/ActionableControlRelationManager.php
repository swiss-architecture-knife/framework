<?php

namespace Swark\DataModel\Auditing\Infrastructure\UI\ActionResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ActionableControlRelationManager extends RelationManager
{
    protected static string $relationship = 'controls';

    protected static ?string $title = 'Actions derived from controls';

    public function form(Form $form): Form
    {
        return $form;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('regulation.name')->label('Regulation'),
                Tables\Columns\TextColumn::make('chapter.name')->label('Regulation chapter'),
                Tables\Columns\TextColumn::make('name')->label('Control'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        $query->leftJoin('regulation_chapter', 'regulation_chapter.id', 'regulation_control.regulation_chapter_id')
                            ->leftJoin('regulation', 'regulation.id', 'regulation_chapter.regulation_id')
                            ->select([
                                'regulation_control.id',
                                'regulation_control.name AS regulation_control_name',
                                'regulation_chapter.name AS regulation_chapter_name',
                                'regulation.name AS regulation_name',
                            ]);
                    })
                    ->recordTitle(function (Model $record): string {
                        return "{$record->regulation_name}:{$record->regulation_chapter_name}:{$record->regulation_control_name}";
                    })
                    ->recordSelectSearchColumns(['regulation_control.name', 'regulation_chapter.name'])
                ,
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
