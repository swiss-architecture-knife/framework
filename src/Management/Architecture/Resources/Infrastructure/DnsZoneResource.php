<?php

namespace Swark\Management\Architecture\Resources\Infrastructure;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Business\UI\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\DataModel\Network\Domain\Entity\DnsZone;
use Swark\Management\Resources\Shared;


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
            'index' => \Swark\Management\Architecture\Resources\Infrastructure\DnsZoneResource\Pages\ListDnsZones::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Infrastructure\DnsZoneResource\Pages\CreateDnsZone::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Infrastructure\DnsZoneResource\Pages\EditDnsZone::route('/{record}/edit'),
        ];
    }
}
