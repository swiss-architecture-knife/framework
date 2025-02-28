<?php

namespace Swark\DataModel\Business\UI\ActorResource\RelationManagers;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Swark\Management\Resources\Shared;

class ZoneRelationManager extends RelationManager
{
    protected static string $relationship = 'zones';

    protected static ?string $title = 'Assigned zones';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::requiredName(),
                Shared::scompId(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('pivot.description')->label('Description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['zone.name'])
                    ->recordTitle(fn(Model $model) => "{$model->name}")
                    ->form(fn(AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Textarea::make('description')->required(false),
                    ]),
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
