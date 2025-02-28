<?php

namespace Swark\DataModel\Operations\Infrastructure\UI;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;
use Swark\DataModel\Operations\Domain\Entity\Stage;

class StageResource extends Resource
{
    protected static ?string $model = Stage::class;

    protected static ?string $navigationGroup = Shared::ECOSYSTEM;
    protected static ?int $navigationSort = 9;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(),
            ]);
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
            'index' => \Swark\DataModel\Operations\Infrastructure\UI\StageResource\Pages\ListStages::route('/'),
            'create' => \Swark\DataModel\Operations\Infrastructure\UI\StageResource\Pages\CreateStage::route('/create'),
            'edit' => \Swark\DataModel\Operations\Infrastructure\UI\StageResource\Pages\EditStage::route('/{record}/edit'),
        ];
    }
}
