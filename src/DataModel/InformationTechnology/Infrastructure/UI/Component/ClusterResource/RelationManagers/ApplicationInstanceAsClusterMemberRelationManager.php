<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Swark\DataModel\Deployment\Domain\Entity\Deployment;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Cluster;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Namespace_;
use Swark\DataModel\InformationTechnology\Domain\Model\Component\ExecutionEnvironment;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\RelationManagers\ApplicationInstanceRelationManager\Tables\Actions\AttachAction;

class ApplicationInstanceAsClusterMemberRelationManager extends RelationManager
{
    protected static string $relationship = 'applicationInstances';

    protected static ?string $title = "Assigned application instances";

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // not relevant, see AttachAction->form():
                // Forms\Components\Checkbox::make('is_primary'),
                // pass ID from parent modal
                Forms\Components\Hidden::make('cluster_id')->default(function (RelationManager $livewire): int {
                    return $livewire->getOwnerRecord()->id;
                }),
            ]);
    }

    public function isReadOnly(): bool
    {
        // do not allow editing for System
        if ($this->getOwnerRecord() instanceof Deployment) {
            return true;
        }

        return false;
    }

    public function table(Table $table): Table
    {
        $columns = [
            Tables\Columns\TextColumn::make('id')
                ->formatStateUsing(fn(string $state, Model $record): string => ExecutionEnvironment::from($record)->executor())
                ->label('Execution on')
            ,
            Tables\Columns\TextColumn::make('release.software.name')
                ->badge()
                // TODO badge is wrong if release has "match any"
                ->color(function (string $state, Model $record): string {
                    if (!$this->getOwnerRecord()->targetRelease?->accepts($record->release)) {
                        return 'danger';
                    }

                    return 'success';
                }),
            Tables\Columns\TextColumn::make(Cluster::NAMESPACE_NAME_JOINED)->label('Namespace'),
        ];

        if ($this->getOwnerRecord() instanceof Cluster) {
            $columns[] = Tables\Columns\TextColumn::make('is_primary')->badge()
                ->formatStateUsing(fn(string $state, Model $record): string => $record->is_primary ? 'Yes' : 'No')
                ->color(function (string $state, Model $record): string {
                    return $record->is_primary ? 'success' : 'info';
                });
        }

        $columns[] = Tables\Columns\TextColumn::make('release.version');
        $columns[] = Tables\Columns\TextColumn::make('stage.name');

        return $table
            ->recordTitleAttribute('name')
            ->columns($columns)
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        return $query
                            ->leftJoin('host', function ($join) {
                                $join->on('application_instance.executor_id', '=', 'host.id');
                                $join->on('application_instance.executor_type', '=', DB::Raw('"host"'));
                            })
                            ->leftJoin('stage', function ($join) {
                                $join->on('application_instance.stage_id', '=', 'stage.id');
                            })
                            ->leftJoin('runtime', function ($join) {
                                $join->on('application_instance.executor_id', '=', 'runtime.id');
                                $join->on('application_instance.executor_type', '=', DB::Raw('"runtime"'));
                            })
                            ->select([
                                'application_instance.id',
                                'release.id AS release_id',
                                'release.version AS release_name',
                                'software.name',
                                'software.id AS software_id',
                                'runtime.name AS runtime_name',
                                'host.name AS host_name',
                                'application_instance.executor_id',
                                'application_instance.executor_type',
                                'namespace.name AS namespace_name',
                                'stage.name',
                            ])
                            // ->leftJoin('namespace', 'namespace.id', 'cluster_member.namespace_id')
                            ->leftJoin('release', 'release.id', 'application_instance.release_id')
                            ->leftJoin('software', 'software.id', 'release.software_id')
                            //  ->where('software.is_runtime', true)
                            ->orderBy('software.name')
                            ->orderBy('release.version');
                    })
                    ->recordTitle(function (Model $record): string {
                        return ExecutionEnvironment::from($record)->title();
                    })
                    ->recordSelectSearchColumns(['software.name', 'host.name', 'runtime.name'])
                    ->form(fn(AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\Checkbox::make('is_primary')
                            ->label('Is primary node in this cluster')
                            ->default(false),

                        Forms\Components\Select::make('namespace_id')
                            ->required(false)
                            ->options(Namespace_::where('cluster_id', $this->getOwnerRecord()->id)->get()->pluck('name', 'id'))
                    ])
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
