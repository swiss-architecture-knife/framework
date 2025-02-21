<?php

namespace Swark\Management\Architecture\Resources\Software;

use App\Management\Resources\Software\ReleaseTrainResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Software\Domain\Model\ReleaseTrain;
use Swark\Management\Architecture\Resources\Software\ReleaseTrainResource\RelationManagers\ReleasesRelationManager;
use Swark\Management\Resources\Shared;

class ReleaseTrainResource extends Resource
{
    protected static ?string $model = ReleaseTrain::class;

    protected static ?string $navigationGroup = Shared::ENTERPRISE_ARCHITECTURE;

    protected static ?int $navigationSort = 3;

    protected static ?string $label = 'Release train';
    protected static ?string $navigationLabel = 'Release trains';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(withScompId: false),
                Forms\Components\Section::make('Parent system')->schema([
                    Forms\Components\Checkbox::make('is_latest')->label('Is latest release for the parent system'),
                    Forms\Components\Select::make('system_id')
                        ->relationship('system', titleAttribute: 'name')
                        ->required(false),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('system.name'),
                Tables\Columns\TextColumn::make('name')->label('Release train'),
                Tables\Columns\TextColumn::make('releases_count')->counts('releases')->label('Assigned software releases'),
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
            ReleasesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\Management\Architecture\Resources\Software\ReleaseTrainResource\Pages\ListReleaseTrains::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Software\ReleaseTrainResource\Pages\CreateReleaseTrain::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Software\ReleaseTrainResource\Pages\EditReleaseTrain::route('/{record}/edit'),
        ];
    }
}
