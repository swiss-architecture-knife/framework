<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Network;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Domain\Model\InformationTechnology\Network\DnsRecordType;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\DnsRecord;
use Swark\DataModel\Presenter\UI\Business\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\DnsRecordResource\Pages\CreateDnsRecord;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\DnsRecordResource\Pages\EditDnsRecord;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\DnsRecordResource\Pages\ListDnsRecords;
use Swark\DataModel\Presenter\UI\Shared;

class DnsRecordResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = DnsRecord::class;

    protected static ?string $navigationGroup = Shared::INFRASTRUCTURE;

    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->generalSection([
                Select::make('dns_zone_id')->relationship(
                    name: 'dnsZone', titleAttribute: 'zone'
                )->label('DNS zone')->required(),
                Forms\Components\TextInput::make('name')->label('Record')->required(),
                Select::make('type')->options(DnsRecordType::toMap()),
                Forms\Components\TextInput::make('data')->label('Data'),
                // TODO IP address id
            ])->decorate();
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
            'index' => ListDnsRecords::route('/'),
            'create' => CreateDnsRecord::route('/create'),
            'edit' => EditDnsRecord::route('/{record}/edit'),
        ];
    }
}

