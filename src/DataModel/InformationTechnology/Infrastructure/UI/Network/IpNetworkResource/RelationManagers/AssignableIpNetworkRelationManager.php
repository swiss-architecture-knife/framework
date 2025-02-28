<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\IpNetworkResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AssignableIpNetworkRelationManager extends RelationManager
{
    protected static string $relationship = 'ipNetworks';

    protected static ?string $title = "Assigned IP networks";

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('network')->label('Network'),
                Tables\Columns\TextColumn::make('network_mask')->label('Mask'),
                Tables\Columns\TextColumn::make('gateway')->label('Gateway'),
                Tables\Columns\TextColumn::make('vlan.number')->label('VLAN'),
                Tables\Columns\TextColumn::make('pivot.description')->label('Description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make('belongsTo')
                    ->recordTitle(fn(Model $model) => "{$model->network}")
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['network', 'scomp_id'])
                    ->form(function (Form $form, Tables\Actions\AttachAction $action) {
                        return [
                            $action->getRecordSelect(),
                            Forms\Components\Textarea::make('description'),
                        ];
                    })
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
