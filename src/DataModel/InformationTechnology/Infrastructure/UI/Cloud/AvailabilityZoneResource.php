<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\InformationTechnology\Domain\Entity\Cloud\AvailabilityZone;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class AvailabilityZoneResource extends Resource
{
    protected static ?string $model = AvailabilityZone::class;

    protected static ?string $navigationGroup = Shared::CLOUD;

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(),
                Section::make('Avavilability zone of region')->schema([
                    Shared::selectRegion(required: true),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('region.managedserviceprovider.name')->label('Service provider'),
                Tables\Columns\TextColumn::make('region.name'),
                Tables\Columns\TextColumn::make('name')->label('Availability Zone'),
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
            'index' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\AvailabilityZoneResource\Pages\ListAvailabilityZones::route('/'),
            'create' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\AvailabilityZoneResource\Pages\CreateAvailabilityZone::route('/create'),
            'edit' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\AvailabilityZoneResource\Pages\EditAvailabilityZone::route('/{record}/edit'),
        ];
    }
}
