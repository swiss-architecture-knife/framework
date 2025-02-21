<?php

namespace Swark\Management\Architecture\Resources\Deployment;

use App\Management\Resources\Deployment\StageResource\Pages;
use App\Management\Resources\Deployment\StageResource\RelationManagers;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Deployment\Domain\Entity\Stage;
use Swark\Management\Resources\Shared;

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
            'index' => \Swark\Management\Architecture\Resources\Deployment\StageResource\Pages\ListStages::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Deployment\StageResource\Pages\CreateStage::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Deployment\StageResource\Pages\EditStage::route('/{record}/edit'),
        ];
    }
}
