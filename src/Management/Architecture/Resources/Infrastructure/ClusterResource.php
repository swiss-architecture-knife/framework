<?php

namespace Swark\Management\Architecture\Resources\Infrastructure;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Swark\DataModel\Business\UI\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\DataModel\Infrastructure\Domain\Entity\Cluster;
use Swark\DataModel\Infrastructure\Domain\Model\ClusterMode;
use Swark\Management\Architecture\Resources\Infrastructure\IpNetworkResource\RelationManagers\AssignableIpNetworkRelationManager;
use Swark\Management\Architecture\Resources\Infrastructure\IpNetworkResource\RelationManagers\IpAddressAssignedRelationManager;
use Swark\Management\Resources\Shared;

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
            \Swark\Management\Architecture\Resources\Infrastructure\ClusterResource\RelationManagers\ResourcesRelationManager::class,
            \Swark\Management\Architecture\Resources\Infrastructure\ClusterResource\RelationManagers\ApplicationInstanceAsClusterMemberRelationManager::class,
            \Swark\Management\Architecture\Resources\Infrastructure\ClusterResource\RelationManagers\DeploymentInClusterRelationManager::class,
            \Swark\Management\Architecture\Resources\Infrastructure\ClusterResource\RelationManagers\RuntimeAsClusterMemberRelationManager::class,
            \Swark\Management\Architecture\Resources\Infrastructure\ClusterResource\RelationManagers\SubscriptionAsClusterMemberRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\Management\Architecture\Resources\Infrastructure\ClusterResource\Pages\ListClusters::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Infrastructure\ClusterResource\Pages\CreateCluster::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Infrastructure\ClusterResource\Pages\EditCluster::route('/{record}/edit'),
        ];
    }
}
