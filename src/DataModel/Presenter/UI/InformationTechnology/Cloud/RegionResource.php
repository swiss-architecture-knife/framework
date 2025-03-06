<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Cloud;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Region;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\Shared;

class RegionResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Region::class;

    protected static ?string $navigationGroup = Shared::CLOUD;

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->sections([
                Section::make('Region of service provider')->schema([
                    Shared::selectServiceProvider(),
                ])
            ])->decorate();
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('managedServiceProvider.name'),
                Tables\Columns\TextColumn::make('name')->label('Region name'),
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
            'index' => RegionResource\Pages\ListRegions::route('/'),
            'create' => RegionResource\Pages\CreateRegion::route('/create'),
            'edit' => RegionResource\Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}
