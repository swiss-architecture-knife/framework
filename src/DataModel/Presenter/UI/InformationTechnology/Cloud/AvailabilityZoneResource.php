<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Cloud;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\AvailabilityZone;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\InformationTechnology\Cloud\AvailabilityZoneResource\Pages\CreateAvailabilityZone;
use Swark\DataModel\Presenter\UI\InformationTechnology\Cloud\AvailabilityZoneResource\Pages\EditAvailabilityZone;
use Swark\DataModel\Presenter\UI\InformationTechnology\Cloud\AvailabilityZoneResource\Pages\ListAvailabilityZones;
use Swark\DataModel\Presenter\UI\Shared;

class AvailabilityZoneResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = AvailabilityZone::class;

    protected static ?string $navigationGroup = Shared::CLOUD;

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->sections([
                Section::make('Region of availability zone')->schema([
                    Shared::selectRegion(required: true),
                ]),
            ])->decorate();
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
            'index' => ListAvailabilityZones::route('/'),
            'create' => CreateAvailabilityZone::route('/create'),
            'edit' => EditAvailabilityZone::route('/{record}/edit'),
        ];
    }
}
