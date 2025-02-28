<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\InformationTechnology\Domain\Entity\ArchitectureType;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class ArchitectureTypeResource extends Resource
{
    protected static ?string $model = ArchitectureType::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = Shared::ECOSYSTEM;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Architecture type'),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\ArchitectureTypeResource\Pages\ListArchitectureTypes::route('/'),
            'create' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\ArchitectureTypeResource\Pages\CreateArchitectureType::route('/create'),
            'edit' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\ArchitectureTypeResource\Pages\EditArchitectureType::route('/{record}/edit'),
        ];
    }
}
