<?php
namespace Swark\Management\Architecture\Resources\Infrastructure\VlanResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BelongingIpNetworksRelationManager extends RelationManager
{
    protected static string $relationship = 'ipNetworks';

    protected static ?string $title = "Belonging IP Networks";

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('network'),
                TextColumn::make('network_mask')->label('Network mask'),
                TextColumn::make('gateway')->label('Gateway'),
            ])
            ->filters([
                //
            ])
            ->actions([
            ]);
    }
}
