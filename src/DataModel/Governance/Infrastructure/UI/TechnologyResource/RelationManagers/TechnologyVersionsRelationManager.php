<?php

namespace Swark\DataModel\Governance\Infrastructure\UI\TechnologyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class TechnologyVersionsRelationManager extends RelationManager
{
    protected static string $relationship = 'versions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General')->schema([
                    Shared::requiredName(enableUnique: false)->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                        $technologyId = $this->getOwnerRecord()->id;
                        return $rule->where('technology_id', $technologyId);
                    }),
                    Forms\Components\Toggle::make('is_latest')->label('Is latest version'),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Version'),
                Tables\Columns\CheckboxColumn::make('is_latest'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
