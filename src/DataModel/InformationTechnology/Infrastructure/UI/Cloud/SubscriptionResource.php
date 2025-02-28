<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Swark\DataModel\Business\Infrastructure\UI\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\DataModel\InformationTechnology\Domain\Entity\Cloud\Offer;
use Swark\DataModel\InformationTechnology\Domain\Entity\Cloud\Subscription;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\RelationManagers\ResourcesRelationManager;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Release;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationGroup = Shared::CLOUD;

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(nameHint: 'You might want to use the subscription or order ID for that one'),
                Section::make('Description')->schema([

                    Forms\Components\TextInput::make('description'),
                ]),
                Section::make('Subscription offered by')->schema([

                    Select::make('managed_offer_id')
                        ->relationship('offering', titleAttribute: 'name')
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $offer = Offer::with(['software'])->find($state);

                            if ($offer) {
                                $softwareVersionId = (int)$get('release_id');

                                if ($softwareVersionId && $softwareVersion = Release::find($softwareVersionId)) {
                                    if ($offer->application_id !== $softwareVersion->software->softwearable_id) {
                                        // aircraft doesn't belong to vendor, so unselect it
                                        $set('release_id', null);
                                    }
                                }
                            }
                        })
                        ->getOptionLabelFromRecordUsing(fn(Offer $record) => "{$record->managedServiceProvider->name}: {$record->name}")
                        ->required(),
                ]),
                Section::make('Subscribed software')->description('You can optionally select a software which this subscription provides')->schema([
                    Select::make('release_id')
                        ->label('Software release')
                        ->relationship(
                            'release',
                            // TODO: Dependent filter for softwareVersion so that only parent version can be selected
                            // @see https://v2.filamentphp.com/tricks/dependent-select-filters
                            // we have to join the parent software's name to it, otherwise release.id gets mixed up with software.id and only one version is shown
                            modifyQueryUsing: fn(Builder $query) => $query
                                ->leftJoin('software', 'software.id', 'release.software_id')
                                ->select(['release.id', 'release.version', 'software.name'])
                        )
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} > {$record->version}")
                ]),
                Section::make('Structure')->schema([
                    Select::make('logical_zone_id')
                        ->relationship('zone', titleAttribute: 'name')->label('Assigned to logical zone'),
                    Shared::selectAvailabilityZone(required: false),
                ]),
                Section::make('Cluster member')->description('This subscription may be part of cluster. Think of managed MySQL or Redis server.')->schema([
                    Select::make('clusters.name')
                        ->relationship('clusters', titleAttribute: 'name')
                        ->multiple(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('offer.managedServiceProvider.name'),
                Tables\Columns\TextColumn::make('offer.name'),
                Tables\Columns\TextColumn::make('name')->label('Subscription name or ID')
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
            ResourcesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\SubscriptionResource\Pages\ListSubscriptions::route('/'),
            'create' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\SubscriptionResource\Pages\CreateSubscription::route('/create'),
            'edit' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\SubscriptionResource\Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
