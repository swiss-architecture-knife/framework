<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\ReleaseTrainResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ReleasesRelationManager extends RelationManager
{
    protected static string $relationship = 'releases';

    protected static ?string $title = "Release train consists of the following software releases ";

    public function form(Form $form): Form
    {
        return $form;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('software.name'),
                Tables\Columns\TextColumn::make('version'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        return $query
                            ->select([
                                'release.id',
                                'software.name AS software_name',
                                'software.id AS software_id',
                                'version AS release_name',
                            ])
                            ->leftJoin('software', 'software.id', 'release.software_id')
                            ->orderBy('software.name');
                    })
                    ->recordTitle(function (Model $record): string {
                        return $record->software_name ." > " . $record->release_name;
                    })
                    ->recordSelectSearchColumns(['version', 'software.name'])
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
