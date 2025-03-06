<?php

namespace Swark\DataModel\Presenter\UI\SoftwareArchitecture;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Domain\Model\SoftwareArchitecture\ReleaseTrain;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\Shared;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\ReleaseTrainResource\Pages\CreateReleaseTrain;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\ReleaseTrainResource\Pages\EditReleaseTrain;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\ReleaseTrainResource\Pages\ListReleaseTrains;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\ReleaseTrainResource\RelationManagers\ReleasesRelationManager;

class ReleaseTrainResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = ReleaseTrain::class;

    protected static ?string $navigationGroup = Shared::ENTERPRISE_ARCHITECTURE;

    protected static ?int $navigationSort = 3;

    protected static ?string $label = 'Release train';
    protected static ?string $navigationLabel = 'Release trains';

    public static function form(Form $form): Form
    {
        return static::decorator($form)->sections([
            Forms\Components\Section::make('Parent system')->schema([
                    Forms\Components\Checkbox::make('is_latest')->label('Is latest release for the parent system'),
                    Forms\Components\Select::make('system_id')
                        ->relationship('system', titleAttribute: 'name')
                        ->required(false),
                ])
            ])
            ->decorate();
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
            'index' => ListReleaseTrains::route('/'),
            'create' => CreateReleaseTrain::route('/create'),
            'edit' => EditReleaseTrain::route('/{record}/edit'),
        ];
    }
}
