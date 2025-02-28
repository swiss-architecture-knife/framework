<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network;

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
use Swark\DataModel\Business\Infrastructure\UI\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\IpNetwork;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class IpNetworkResource extends Resource
{
    protected static ?string $model = IpNetwork::class;

    protected static ?string $navigationGroup = Shared::INFRASTRUCTURE;

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General')->schema([
                    Shared::scompId()->label('Scomp-ID')->hint('A Scomp-ID can be used for retrieving elements through a hierarchy'),
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
                    Forms\Components\TextInput::make('gateway')->label('Gateway'),
                    Select::make('vlan_id')->relationship(
                        name: 'vlan', titleAttribute: 'number'
                    )->label('Assigned to VLAN'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('scomp_id'),
                Tables\Columns\TextColumn::make('network')->label('Network'),
                Tables\Columns\TextColumn::make('network_mask')->label('Mask'),
                Tables\Columns\TextColumn::make('gateway')->label('Gateway'),

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
            'index' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\IpNetworkResource\Pages\ListIpNetworks::route('/'),
            'create' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\IpNetworkResource\Pages\CreateIpNetwork::route('/create'),
            'edit' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\IpNetworkResource\Pages\EditIpNetwork::route('/{record}/edit'),
        ];
    }
}


class HasIpAddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'ipAddresses';

    protected static ?string $title = "IP addresses";
    protected static ?string $inverseRelationship = 'ipNetwork';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('recordId')->default(function (RelationManager $livewire): int {
                return $livewire->getOwnerRecord()->id;
            }),
            TextInput::make('address')->required(),
            Forms\Components\Textarea::make('description'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('address')
            ->columns([
                Tables\Columns\TextColumn::make('address'),
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->inverseRelationship('ipNetwork');
    }
}
