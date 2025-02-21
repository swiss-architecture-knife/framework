<?php

namespace Swark\Management\Architecture\Resources\Infrastructure;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Swark\DataModel\Cloud\Domain\Entity\Account;
use Swark\DataModel\Cloud\Domain\Entity\Offer;
use Swark\DataModel\Infrastructure\Domain\Entity\Baremetal;
use Swark\Management\Architecture\Resources\Ecosystem\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\Management\Architecture\Resources\Infrastructure\BaremetalResource\Pages\CreateBaremetalNic;
use Swark\Management\Architecture\Resources\Infrastructure\BaremetalResource\Pages\EditBaremetal;
use Swark\Management\Architecture\Resources\Infrastructure\BaremetalResource\Pages\ManageBaremetalNics;
use Swark\Management\Resources\Shared;

class BaremetalResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Baremetal::class;

    protected static ?string $navigationGroup = Shared::INFRASTRUCTURE;

    protected static ?int $navigationSort = 10;

    public const IS_MANAGED_VIRTUAL_FIELD = 'is_managed';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(nameHint: "You might use the server's serial number or EC2 instance name for this"),
                /*
                Shared::selectCustomer()->label('Reserved for customer'),
                Select::make('logical_zone_id')
                    ->relationship('zone', titleAttribute: 'name'),
                Shared::selectAvailabilityZone(required: false),
                */
                // user can select, if this baremetal is managed. That means, the baremetal system is inside a datacenter or a virtual host (e.g. EC2 instance)
                // previously, I tried something like virtual attributes/accessors directly in the Baremetal model, but that did not work.
                Forms\Components\Checkbox::make(static::IS_MANAGED_VIRTUAL_FIELD)
                    // we make it live() so our fieldset/one-to-one relation can listen to changing events
                    ->live()
                    ->label('Baremetal is managed by a service provider')
                ,
                Section::make('Managed by a service provider')
                    // fieldset is only visible if "managed" checkbox is checked
                    // if visibility is false, it implicitly deletes the "managed" relationship
                    ->visible(fn(Get $get): bool => $get(static::IS_MANAGED_VIRTUAL_FIELD))
                    ->relationship('managed',
                    /*
                    condition: function (?Component $component): bool {
                        $parentState = $component->getContainer()->getRawState();
                        //var_dump($parentState);
                        //die("LA");
                        return filled($parentState['is_managed']);
                    },*/
                    )->schema([
                        Select::make('managed_offer_id')->relationship('offer', titleAttribute: 'name')
                            ->required()
                            ->label('Service offer')
                            ->getOptionLabelFromRecordUsing(fn(Offer $record) => "{$record->managedServiceProvider->name}: {$record->name}"),
                        Select::make('managed_account_id')->relationship('account', titleAttribute: 'name')->label('Account, Datacenter, Scope')
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn(Account $record) => "{$record->managedServiceProvider->name}: {$record->name}"),
                        Shared::selectAvailabilityZone(required: true)
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('zone.name'),
                Tables\Columns\TextColumn::make('customer.name')->label('Reserved for customer'),

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

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EditBaremetal::class,
            ManageBaremetalNics::class
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\Management\Architecture\Resources\Infrastructure\BaremetalResource\Pages\ListBaremetals::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Infrastructure\BaremetalResource\Pages\CreateBaremetal::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Infrastructure\BaremetalResource\Pages\EditBaremetal::route('/{record}/edit'),

            'nics' => ManageBaremetalNics::route('/{record}/nics'),
            'nics.create' => CreateBaremetalNic::route('/{record}/nics/create'),
        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }
}
