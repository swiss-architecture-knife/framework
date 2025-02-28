<?php

namespace Swark\DataModel\Auditing\Infrastructure\UI\ActionResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ActionableObjectiveRelationManager extends RelationManager
{
    protected static string $relationship = 'objectives';

    protected static ?string $title = 'Actions derived from objectives';

    public function form(Form $form): Form
    {
        return $form;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('strategy.name')->label('Strategy'),
                Tables\Columns\TextColumn::make('name')->label('Objective'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        $query->leftJoin('strategy', 'strategy.id', 'objective.strategy_id')
                            ->select([
                                'objective.id',
                                'objective.name AS objective_name',
                                'strategy.name AS strategy_name',
                            ]);
                    })
                    ->recordTitle(function (Model $record): string {
                        return "{$record->strategy_name}:{$record->objective_name}";
                    })
                    ->recordSelectSearchColumns(['objective.name'])
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
