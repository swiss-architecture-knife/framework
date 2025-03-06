<?php

namespace Swark\DataModel\Presenter\UI\Governance;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Domain\Model\Governance\TechnologyType;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Technology;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\DataModelDecorator;
use Swark\DataModel\Presenter\UI\Governance\TechnologyResource\Pages\CreateTechnology;
use Swark\DataModel\Presenter\UI\Governance\TechnologyResource\Pages\EditTechnology;
use Swark\DataModel\Presenter\UI\Governance\TechnologyResource\Pages\ListTechnologies;
use Swark\DataModel\Presenter\UI\Governance\TechnologyResource\RelationManagers\TechnologyVersionsRelationManager;
use Swark\DataModel\Presenter\UI\Shared;

class TechnologyResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Technology::class;

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->generalSection([
                Forms\Components\Select::make('type')->options(TechnologyType::toMap()),
            ])
            ->decorate();
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
            TechnologyVersionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTechnologies::route('/'),
            'create' => CreateTechnology::route('/create'),
            'edit' => EditTechnology::route('/{record}/edit'),
        ];
    }
}
