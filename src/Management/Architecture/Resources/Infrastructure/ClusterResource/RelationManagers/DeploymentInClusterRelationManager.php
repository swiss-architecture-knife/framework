<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\ClusterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Infrastructure\Domain\Entity\Cluster;
use Swark\DataModel\Infrastructure\Domain\Entity\Namespace_;
use Swark\Management\Resources\Shared;

class DeploymentInClusterRelationManager extends RelationManager
{
    protected static string $relationship = 'deployments';

    protected static ?string $title = "Deployments";

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(withScompId: true),

                Forms\Components\Select::make('namespace_id')
                    ->required(false)
                    ->options(Namespace_::where('cluster_id', $this->getOwnerRecord()->id)->get()->pluck('name', 'id')),

                Forms\Components\Select::make('release_train_id')
                    ->relationship('releaseTrain')
                    ->required(false)
                    ->getOptionLabelFromRecordUsing(function (Model $record) {
                        $prefix = "";
                        if ($record->system) {
                            $prefix = $record->system->name . ": ";
                        }
                        return $prefix . $record->name;
                    }),

                // not relevant, see AttachAction->form():
                // Forms\Components\Checkbox::make('is_primary'),
                // pass ID from parent modal
                Forms\Components\Hidden::make('cluster_id')->default(function (RelationManager $livewire): int {
                    return $livewire->getOwnerRecord()->id;
                }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('releaseTrain.system.name')->label('Parent system name'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make(Cluster::NAMESPACE_NAME_JOINED)->label('Namespace'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
