<?php

namespace Swark\DataModel\Compliance\UI;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Compliance\Domain\Entity\Regulation;
use Swark\Management\Resources\Shared;

class RegulationResource extends Resource
{
    protected static ?string $model = Regulation::class;


    protected static ?string $navigationGroup = Shared::COMPLIANCE;

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(nameHint: "Regulation's name"),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
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
            \Swark\DataModel\Compliance\UI\RegulationResource\RelationManagers\ChapterRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\DataModel\Compliance\UI\RegulationResource\Pages\ListRegulations::route('/'),
            'create' => \Swark\DataModel\Compliance\UI\RegulationResource\Pages\CreateRegulation::route('/create'),
            'edit' => \Swark\DataModel\Compliance\UI\RegulationResource\Pages\EditRegulation::route('/{record}/edit'),
        ];
    }
}
