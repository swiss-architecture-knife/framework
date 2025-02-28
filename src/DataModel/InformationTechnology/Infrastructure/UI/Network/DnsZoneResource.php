<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Business\Infrastructure\UI\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\DnsZone;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;


class DnsZoneResource extends Resource
{
    protected static ?string $model = DnsZone::class;

    protected static ?string $navigationGroup = Shared::INFRASTRUCTURE;

    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General')->schema([
                    Forms\Components\TextInput::make('zone')->nullable(),
                    Select::make('parent_dns_zone_id')->relationship(
                        name: 'parentDnsZone', titleAttribute: 'zone'
                    )->label('Parent zone'),
                ])
            ]);
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
            'index' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\DnsZoneResource\Pages\ListDnsZones::route('/'),
            'create' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\DnsZoneResource\Pages\CreateDnsZone::route('/create'),
            'edit' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\DnsZoneResource\Pages\EditDnsZone::route('/{record}/edit'),
        ];
    }
}
