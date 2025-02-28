<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Layer;

class LayerResource extends Resource
{
    protected static ?string $model = Layer::class;

    protected static ?string $navigationGroup = Shared::SOFTWARE;
    protected static ?int $navigationSort = 1;


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
                Tables\Columns\TextColumn::make('name')->label('Layer'),
                TextColumn::make('components_count')->label('Used by components')->counts('components'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\LayerResource\Pages\ListLayers::route('/'),
            'create' => \Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\LayerResource\Pages\CreateLayer::route('/create'),
            'edit' => \Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\LayerResource\Pages\EditLayer::route('/{record}/edit'),
        ];
    }
}
