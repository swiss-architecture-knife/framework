<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Business\Infrastructure\UI\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\DataModel\InformationTechnology\Domain\Entity\Zone;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class ZoneResource extends Resource
{
    protected static ?string $model = Zone::class;

    protected static ?string $navigationGroup = Shared::ECOSYSTEM;

    protected static ?int $navigationSort = 10;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(
                    clazz: self::$model,
                    additionalSchema: [
                        Select::make('dataClassification')->relationship('dataClassification', 'name'),
                    ]
                ),
            ]);
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
            'index' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\ZoneResource\Pages\ListZones::route('/'),
            'create' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\ZoneResource\Pages\CreateZone::route('/create'),
            'edit' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\ZoneResource\Pages\EditZone::route('/{record}/edit'),
        ];
    }
}
