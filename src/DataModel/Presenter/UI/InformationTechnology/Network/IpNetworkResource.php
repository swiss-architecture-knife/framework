<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Network;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\IpNetwork;
use Swark\DataModel\Presenter\UI\Business\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\IpNetworkResource\Pages\CreateIpNetwork;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\IpNetworkResource\Pages\EditIpNetwork;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\IpNetworkResource\Pages\ListIpNetworks;
use Swark\DataModel\Presenter\UI\Shared;

class IpNetworkResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = IpNetwork::class;

    protected static ?string $navigationGroup = Shared::INFRASTRUCTURE;

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->generalSection([
                Textarea::make('description')->nullable(),
                Select::make('type')->options(['4' => 'IPv4', '6' => 'IPv6'])->label('Type'),
                Forms\Components\TextInput::make('network')
                    ->label('Network')
                    ->hint('e.g. 10.0.0.0')
                    ->required()
                    ->ip(),
                Forms\Components\TextInput::make('network_mask')
                    ->label('Network mask')
                    ->hint('e.g. 10.0.0.0')
                    ->required()
                    ->ip(),
                // TODO Switch to select
                Forms\Components\TextInput::make('gateway')->label('Gateway'),
                Select::make('vlan_id')->relationship(
                    name: 'vlan', titleAttribute: 'number'
                )->label('Assigned to VLAN'),
            ])
            ->decorate();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('network')->label('Network'),
                Tables\Columns\TextColumn::make('network_mask')->label('Mask'),
                Tables\Columns\TextColumn::make('gateway.address')->label('Gateway'),

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
            HasIpAddressesRelationManager::class,
            AssociatedWithOrganizationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIpNetworks::route('/'),
            'create' => CreateIpNetwork::route('/create'),
            'edit' => EditIpNetwork::route('/{record}/edit'),
        ];
    }
}
