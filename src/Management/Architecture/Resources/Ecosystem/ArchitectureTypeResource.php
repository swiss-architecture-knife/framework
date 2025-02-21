<?php

namespace Swark\Management\Architecture\Resources\Ecosystem;

use App\Management\Resources\Ecosystem\ArchitectureTypeResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Ecosystem\Domain\Entity\ArchitectureType;
use Swark\Management\Resources\Shared;

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
            'index' => \Swark\Management\Architecture\Resources\Ecosystem\ArchitectureTypeResource\Pages\ListArchitectureTypes::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Ecosystem\ArchitectureTypeResource\Pages\CreateArchitectureType::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Ecosystem\ArchitectureTypeResource\Pages\EditArchitectureType::route('/{record}/edit'),
        ];
    }
}
