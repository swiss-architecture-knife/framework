<?php

namespace Swark\Management\Architecture\Resources\Deployment\DeploymentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\Management\Resources\Shared;

class AttachableResourcesRelationManager extends RelationManager
{
    protected static string $relationship = 'resources';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::requiredName(),
                Forms\Components\Select::make('resource_type_id')
                    ->relationship('resourceType', titleAttribute: 'name')
                    ->required()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('resourceType.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
