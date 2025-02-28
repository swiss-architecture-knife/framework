<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\InformationTechnology\Domain\Entity\Cloud\Region;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

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
            'index' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\RegionResource\Pages\ListRegions::route('/'),
            'create' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\RegionResource\Pages\CreateRegion::route('/create'),
            'edit' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\RegionResource\Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}
