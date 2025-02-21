<?php

namespace Swark\Management\Architecture\Resources\Enterprise\SystemResource\RelationManagers;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SoftwareForSystemRelationManager extends RelationManager
{
    protected static string $relationship = 'softwares';
    protected static ?string $inverseRelationship = 'systems';


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Software'),
                Tables\Columns\TextColumn::make('description')->label('Description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['software.name'])
                ->form(function(Form $form, Tables\Actions\AttachAction $action) {
                    return [
                        $action->getRecordSelect(),
                        Textarea::make('name'),
                        Textarea::make('description'),
                    ];
                })
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
