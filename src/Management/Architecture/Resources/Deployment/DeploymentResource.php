<?php

namespace Swark\Management\Architecture\Resources\Deployment;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Swark\DataModel\Deployment\Domain\Entity\Deployment;
use Swark\Management\Architecture\Resources\Deployment\DeploymentResource\RelationManagers\AttachableResourcesRelationManager;
use Swark\Management\Architecture\Resources\Infrastructure\ClusterResource\RelationManagers\ApplicationInstanceAsClusterMemberRelationManager;
use Swark\Management\Architecture\Resources\Infrastructure\ClusterResource\RelationManagers\SubscriptionAsClusterMemberRelationManager;
use Swark\Management\Resources\Shared;

class DeploymentResource extends Resource
{
    protected static ?string $model = Deployment::class;

    protected static ?string $navigationGroup = Shared::DEPLOYMENT;
    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(),
                Section::make('Relationships')->schema([
                    Shared::selectReleaseTrain()->disabledOn('edit'),
                    Select::make('stage')
                        ->label('Stage')
                        ->relationship('stage', titleAttribute: 'name'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('system_name')->label('Parent system'),
                Tables\Columns\TextColumn::make('release_train_name')->label('Release train'),
                Tables\Columns\TextColumn::make('cluster_name')->label('Deployed to cluster'),
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
            ])
            // we have to use our own query here. Filament's relationship manager for tables does not support hasManyThrough or morph relations for our specific case
            ->modifyQueryUsing(function (Builder $query) {
                return $query->select([
                    'deployment.id AS id',
                    'release_train.name AS release_train_name',
                    'system.name AS system_name',
                    'cluster.name AS cluster_name'
                ])
                    ->leftJoin('release_train', 'release_train.id', 'deployment.release_train_id')
                    ->leftJoin('system', 'system.id', 'release_train.system_id')
                    ->leftJoin('cluster_member', function ($join) {
                        $join->on('cluster_member.member_id', '=', 'deployment.id');
                        $join->on('cluster_member.member_type', '=', DB::Raw('"deployment"'));
                    })
                    ->leftJoin('cluster', 'cluster.id', 'cluster_member.cluster_id');
            });
    }

    public static function getRelations(): array
    {
        return [
            AttachableResourcesRelationManager::class,
            ApplicationInstanceAsClusterMemberRelationManager::class,
            SubscriptionAsClusterMemberRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\Management\Architecture\Resources\Deployment\DeploymentResource\Pages\ListDeployments::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Deployment\DeploymentResource\Pages\CreateDeployment::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Deployment\DeploymentResource\Pages\EditDeployment::route('/{record}/edit'),
        ];
    }
}
