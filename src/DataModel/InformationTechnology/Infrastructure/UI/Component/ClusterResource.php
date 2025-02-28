<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Swark\DataModel\Business\Infrastructure\UI\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Cluster;
use Swark\DataModel\InformationTechnology\Domain\Model\Component\ClusterMode;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\IpNetworkResource\RelationManagers\AssignableIpNetworkRelationManager;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\IpNetworkResource\RelationManagers\IpAddressAssignedRelationManager;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class ClusterResource extends Resource
{
    protected static ?string $model = Cluster::class;

    protected static ?string $navigationGroup = Shared::INFRASTRUCTURE;

    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(),

                Forms\Components\Section::make('Restrictions')->schema([
                    Select::make('target_release_id')
                        ->label('Restrict software in this cluster')
                        ->hint('You can restrict the software assignable to this cluster')
                        ->relationship('targetRelease', 'software.name',
                            modifyQueryUsing: fn(Builder $query) => $query
                                ->leftJoin('software', 'software.id', 'release.software_id')
                                ->select(['release.id', 'release.version', 'software.name'])
                                ->orderBy('software.name')
                                ->orderBy('release.version')
                        )
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} > {$record->version}")
                        ->nullable(),
                ]),
                Forms\Components\Section::make('Cluster options')->schema([
                    Forms\Components\Select::make('stage')
                        ->relationship('stage', 'name'),
                    Select::make('mode')
                        ->label('Mode of operation')
                        ->hint('Loadbalancing goes to every host, replica is a cold stand-by instance, failover is a hot stand-by instance')
                        ->options(ClusterMode::toMap())
                        ->nullable(),

                    Forms\Components\TextInput::make('virtual_name')
                        ->label('Virtual hostname or IP')
                        ->hint('Virtual IP or hostname is used by clients to connect to the active target instance')
                        ->nullable()
                ]),
                Forms\Components\Section::make('Namespaces')->schema([

                    Repeater::make('namespaces')->relationship('namespaces')->schema([
                        Forms\Components\TextInput::make('name')->required()
                    ])
                        ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                        ->collapsed(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
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
            AssignableIpNetworkRelationManager::class,
            IpAddressAssignedRelationManager::class,
            AssociatedWithOrganizationsRelationManager::class,
            \Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\RelationManagers\ResourcesRelationManager::class,
            \Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\RelationManagers\ApplicationInstanceAsClusterMemberRelationManager::class,
            \Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\RelationManagers\DeploymentInClusterRelationManager::class,
            \Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\RelationManagers\RuntimeAsClusterMemberRelationManager::class,
            \Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\RelationManagers\SubscriptionAsClusterMemberRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\Pages\ListClusters::route('/'),
            'create' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\Pages\CreateCluster::route('/create'),
            'edit' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\Pages\EditCluster::route('/{record}/edit'),
        ];
    }
}
