<?php

namespace Swark\Management\Architecture\Resources\Enterprise\SystemResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ApplicationInstanceRelationManager extends RelationManager
{
    protected static string $relationship = 'applicationInstances';
    protected static ?string $inverseRelationship = 'system';

    protected static ?string $title = 'Application instances';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('scomp_id')
            ->columns([
                Tables\Columns\TextColumn::make('scomp_id')->label('ID'),
                Tables\Columns\TextColumn::make('release.software.name')->label('Software'),
                Tables\Columns\TextColumn::make('release.version')->label('Release'),
                Tables\Columns\TextColumn::make('executor.name')->label('Executed on'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AssociateAction::make()
            ])
            ->actions([
                Tables\Actions\DissociateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                ]),
            ]);
    }
}
