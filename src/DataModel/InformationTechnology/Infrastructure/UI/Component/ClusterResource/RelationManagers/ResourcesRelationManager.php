<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class ResourcesRelationManager extends RelationManager
{
    protected static string $relationship = 'resources';

    protected static ?string $title = "Provided resources";

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::requiredName(),
                Forms\Components\Textarea::make('description')->nullable(true),
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
