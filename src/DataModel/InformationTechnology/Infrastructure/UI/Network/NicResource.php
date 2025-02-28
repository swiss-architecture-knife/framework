<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\Nic;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\IpNetworkResource\RelationManagers\IpAddressAssignedRelationManager;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\NicResource\Pages\CreateNic;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\NicResource\Pages\EditNic;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class NicResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Nic::class;

    public static function getRelations(): array
    {
        return [
            IpAddressAssignedRelationManager::class,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(nameHint: "You might use the server's serial number or EC2 instance name for this"),
                TextInput::make('mac_address')->label('MAC address'),
                Shared::selectVendor(required: false),
                Select::make('vlan_id')->relationship(name: 'vlan', titleAttribute: 'number'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateNic::route('/create'),
            'edit' => EditNic::route('/{record}/edit'),
        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        // Configure the ancestor (parent) relationship here
        return Ancestor::make(
            'nics', // Relationship name
            'equipable', // Inverse relationship name
        );
    }
}
