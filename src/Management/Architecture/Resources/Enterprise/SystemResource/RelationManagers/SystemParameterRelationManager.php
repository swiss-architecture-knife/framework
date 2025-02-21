<?php

namespace Swark\Management\Architecture\Resources\Enterprise\SystemResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SystemParameterRelationManager extends RelationManager
{
    protected static string $relationship = 'metrics';
    protected static ?string $inverseRelationship = 'systems';

    protected static ?string $title = 'Parameter';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Metric'),
                Tables\Columns\TextColumn::make('value')->label('Value'),
                Tables\Columns\TextColumn::make('pivot.description')->label('Description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(function(Builder $query) {
                        $query->where('is_system_parameter', 1);
                    })
                    ->recordSelectSearchColumns(['metric.name'])
                    ->form(function (Form $form, Tables\Actions\AttachAction $action) {
                        return [
                            $action->getRecordSelect(),
                            TextInput::make('value')->name('value')->label('Value')->required(),
                            TextInput::make('description')->name('description')->required(),
                        ];
                    })
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
