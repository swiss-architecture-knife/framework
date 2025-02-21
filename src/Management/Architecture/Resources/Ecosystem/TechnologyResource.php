<?php

namespace Swark\Management\Architecture\Resources\Ecosystem;

use App\Management\Resources\Ecosystem\TechnologyResource\Pages;
use App\Management\Resources\Ecosystem\TechnologyResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Ecosystem\Domain\Entity\Technology;
use Swark\DataModel\Ecosystem\Domain\Model\TechnologyType;
use Swark\Management\Resources\Shared;

class TechnologyResource extends Resource
{
    protected static ?string $model = Technology::class;

    protected static ?string $navigationGroup = Shared::ECOSYSTEM;

    protected static ?int $navigationSort = 6;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(),
                Forms\Components\Select::make('type')->options(TechnologyType::toMap()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Technology'),
                Tables\Columns\TextColumn::make('type')->label('Type'),
                Tables\Columns\TextColumn::make('versions_count')->counts('versions')
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
            \Swark\Management\Architecture\Resources\Ecosystem\TechnologyResource\RelationManagers\TechnologyVersionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\Management\Architecture\Resources\Ecosystem\TechnologyResource\Pages\ListTechnologies::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Ecosystem\TechnologyResource\Pages\CreateTechnology::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Ecosystem\TechnologyResource\Pages\EditTechnology::route('/{record}/edit'),
        ];
    }
}
