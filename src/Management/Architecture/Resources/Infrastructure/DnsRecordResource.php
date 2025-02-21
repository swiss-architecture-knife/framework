<?php

namespace Swark\Management\Architecture\Resources\Infrastructure;

use App\Management\Resources\Infrastructure\BaremetalResource\Pages;
use App\Management\Resources\Infrastructure\BaremetalResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Network\Domain\Entity\DnsRecord;
use Swark\DataModel\Network\Domain\Model\DnsRecordType;
use Swark\Management\Architecture\Resources\Ecosystem\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\Management\Resources\Shared;

class DnsRecordResource extends Resource
{
    protected static ?string $model = DnsRecord::class;

    protected static ?string $navigationGroup = Shared::INFRASTRUCTURE;

    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General')->schema([
                    Select::make('dns_zone_id')->relationship(
                        name: 'dnsZone', titleAttribute: 'zone'
                    )->label('DNS zone')->required(),
                    Forms\Components\TextInput::make('name')->label('Record')->required(),
                    Select::make('type')->options(DnsRecordType::toMap()),
                    Forms\Components\TextInput::make('data')->label('Data'),
                ]),
                // TODO IP address id
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Record'),
                Tables\Columns\TextColumn::make('type')->label('Type'),
                Tables\Columns\TextColumn::make('data')->label('Data'),
                Tables\Columns\TextColumn::make('dnsZone.zone')->label('Zone'),

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
            'index' => \Swark\Management\Architecture\Resources\Infrastructure\DnsRecordResource\Pages\ListDnsRecords::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Infrastructure\DnsRecordResource\Pages\CreateDnsRecord::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Infrastructure\DnsRecordResource\Pages\EditDnsRecord::route('/{record}/edit'),
        ];
    }
}

