<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Zone;
use Swark\DataModel\Presenter\UI\Business\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\Shared;

class ZoneResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Zone::class;

    protected static ?string $navigationGroup = Shared::ECOSYSTEM;

    protected static ?int $navigationSort = 10;


    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->generalSection([
                Select::make('dataClassification')->relationship('dataClassification', 'name'),

            ])
            ->decorate();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
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

    public
    static function getRelations(): array
    {
        return [
            AssociatedWithOrganizationsRelationManager::class,
        ];
    }

    public
    static function getPages(): array
    {
        return [
            'index' => ZoneResource\Pages\ListZones::route('/'),
            'create' => ZoneResource\Pages\CreateZone::route('/create'),
            'edit' => ZoneResource\Pages\EditZone::route('/{record}/edit'),
        ];
    }
}
