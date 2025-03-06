<?php

namespace Swark\DataModel\Presenter\UI\Compliance;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Regulation;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\DataModelDecorator;
use Swark\DataModel\Presenter\UI\Shared;

class RegulationResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Regulation::class;

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->nameHint("Regulation's name")
            ->decorate();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
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
            \Swark\DataModel\Presenter\UI\Compliance\RegulationResource\RelationManagers\ChapterRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\DataModel\Presenter\UI\Compliance\RegulationResource\Pages\ListRegulations::route('/'),
            'create' => \Swark\DataModel\Presenter\UI\Compliance\RegulationResource\Pages\CreateRegulation::route('/create'),
            'edit' => \Swark\DataModel\Presenter\UI\Compliance\RegulationResource\Pages\EditRegulation::route('/{record}/edit'),
        ];
    }
}
