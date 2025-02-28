<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\SystemResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Swark\DataModel\Compliance\Domain\Entity\ProtectionGoalLevel;

class ProtectionGoalRelationManager extends RelationManager
{
    protected static string $relationship = 'protectionGoals';
    protected static ?string $inverseRelationship = 'systems';

    protected static ?string $title = 'Protection goals';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Protection goal'),
                Tables\Columns\TextColumn::make('pivot.protection_goal_level_id')->label('Level'),
                Tables\Columns\TextColumn::make('pivot.description')->label('Description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        //$query->where('is_system_parameter', 1);
                    })
                    ->recordSelectSearchColumns(['metric.name'])
                    ->form(function (Form $form, Tables\Actions\AttachAction $action) {
                        return [
                            $action->getRecordSelect()
                            ->reactive()
                            ->afterStateUpdated(fn(Set $set) => $set('protection_goal_level_id', null)),
                            Select::make('protectionGoalLevelId')
                                ->name('protection_goal_level_id')
                                ->options(fn(Get $get): \Illuminate\Support\Collection =>
                                    ProtectionGoalLevel::query()
                                        ->where('protection_goal_id', $get('recordId'))
                                        ->pluck('name', 'id')),
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
