<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\ClusterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Deployment\Domain\Entity\Deployment;

class SubscriptionAsClusterMemberRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // pass ID from parent modal
                Forms\Components\Hidden::make('cluster_id')->default(function (RelationManager $livewire): int {
                    return $livewire->getOwnerRecord()->id;
                }),
            ]);
    }

    public function canCreate(): bool
    {
        if ($this->getOwnerRecord() instanceof Deployment) {
            return false;
        }

        return parent::canCreate();
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
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
