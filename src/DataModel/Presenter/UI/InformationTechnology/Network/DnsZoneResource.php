<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Network;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\DnsZone;
use Swark\DataModel\Presenter\UI\Business\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\DnsZoneResource\Pages\CreateDnsZone;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\DnsZoneResource\Pages\EditDnsZone;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\DnsZoneResource\Pages\ListDnsZones;
use Swark\DataModel\Presenter\UI\Shared;


class DnsZoneResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = DnsZone::class;

    protected static ?string $navigationGroup = Shared::INFRASTRUCTURE;

    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->generalSection([
                Forms\Components\TextInput::make('zone')->nullable(),
                    Select::make('parent_dns_zone_id')->relationship(
                        name: 'parentDnsZone', titleAttribute: 'zone'
                    )->label('Parent zone'),
                ])
            ->decorate();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('zone')->label('Zone'),
                Tables\Columns\TextColumn::make('parentDnsZone.zone')->label('Parent zone'),

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
            AssociatedWithOrganizationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDnsZones::route('/'),
            'create' => CreateDnsZone::route('/create'),
            'edit' => EditDnsZone::route('/{record}/edit'),
        ];
    }
}
