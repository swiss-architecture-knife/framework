<?php

namespace Swark\Management\Architecture\Resources\Cloud;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Cloud\Domain\Entity\Region;
use Swark\Management\Resources\Shared;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $navigationGroup = Shared::CLOUD;

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(),
                Section::make('Region of service provider')->schema([
                    Shared::selectServiceProvider(),
                ])
            ]);
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
            'index' => \Swark\Management\Architecture\Resources\Cloud\RegionResource\Pages\ListRegions::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Cloud\RegionResource\Pages\CreateRegion::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Cloud\RegionResource\Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}
