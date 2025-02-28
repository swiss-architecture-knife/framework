<?php

namespace Swark\DataModel\Governance\Infrastructure\UI;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Governance\Domain\Entity\Technology;
use Swark\DataModel\Governance\Domain\Model\TechnologyType;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class TechnologyResource extends Resource
{
    protected static ?string $model = Technology::class;

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(),
                Forms\Components\Select::make('type')->options(TechnologyType::toMap()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Technology'),
                Tables\Columns\TextColumn::make('type')->label('Type'),
                Tables\Columns\TextColumn::make('versions_count')->counts('versions')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \Swark\DataModel\Governance\Infrastructure\UI\TechnologyResource\RelationManagers\TechnologyVersionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\DataModel\Governance\Infrastructure\UI\TechnologyResource\Pages\ListTechnologies::route('/'),
            'create' => \Swark\DataModel\Governance\Infrastructure\UI\TechnologyResource\Pages\CreateTechnology::route('/create'),
            'edit' => \Swark\DataModel\Governance\Infrastructure\UI\TechnologyResource\Pages\EditTechnology::route('/{record}/edit'),
        ];
    }
}
