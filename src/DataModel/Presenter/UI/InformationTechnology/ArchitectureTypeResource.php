<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\ArchitectureType;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\Shared;

class ArchitectureTypeResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = ArchitectureType::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = Shared::ECOSYSTEM;

    public static function form(Form $form): Form
    {
        return static::decorator($form)->decorate();
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
            'index' => ArchitectureTypeResources\Pages\ListArchitectureTypes::route('/'),
            'create' => ArchitectureTypeResources\Pages\CreateArchitectureType::route('/create'),
            'edit' => ArchitectureTypeResources\Pages\EditArchitectureType::route('/{record}/edit'),
        ];
    }
}
