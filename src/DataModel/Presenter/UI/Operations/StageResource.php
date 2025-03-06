<?php

namespace Swark\DataModel\Presenter\UI\Operations;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\Stage;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\Operations\StageResource\Pages\CreateStage;
use Swark\DataModel\Presenter\UI\Operations\StageResource\Pages\EditStage;
use Swark\DataModel\Presenter\UI\Operations\StageResource\Pages\ListStages;
use Swark\DataModel\Presenter\UI\Shared;

class StageResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Stage::class;

    protected static ?string $navigationGroup = Shared::ECOSYSTEM;
    protected static ?int $navigationSort = 9;


    public static function form(Form $form): Form
    {
        return static::decorator($form)->decorate();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Stage'),
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
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStages::route('/'),
            'create' => CreateStage::route('/create'),
            'edit' => EditStage::route('/{record}/edit'),
        ];
    }
}
