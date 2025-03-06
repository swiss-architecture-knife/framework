<?php

namespace Swark\DataModel\Presenter\UI\SoftwareArchitecture;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Layer;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\Shared;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\LayerResource\Pages\CreateLayer;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\LayerResource\Pages\EditLayer;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\LayerResource\Pages\ListLayers;

class LayerResource extends Resource
{
    use HasDataModelDecorator;
    protected static ?string $model = Layer::class;

    protected static ?string $navigationGroup = Shared::SOFTWARE;
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->sections([
            ])->decorate();
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
            'index' => ListLayers::route('/'),
            'create' => CreateLayer::route('/create'),
            'edit' => EditLayer::route('/{record}/edit'),
        ];
    }
}
