<?php

namespace Swark\DataModel\Auditing\Infrastructure\UI;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Auditing\Domain\Entity\Action;
use Swark\DataModel\Auditing\Domain\Model\FindingStatus;
use Swark\DataModel\Auditing\Infrastructure\UI\ActionResource\RelationManagers\ActionableControlRelationManager;
use Swark\DataModel\Auditing\Infrastructure\UI\ActionResource\RelationManagers\ActionableObjectiveRelationManager;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class ActionResource extends Resource
{
    protected static ?string $model = Action::class;

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(nameHint: 'Summary of what to do'),
                Forms\Components\Section::make('Details')->schema([
                    Forms\Components\MarkdownEditor::make('description'),
                ]),
                Forms\Components\Section::make('Tracing')->schema([
                    Select::make('status')->options(FindingStatus::toMap())->nullable()->label('Status'),
                    DatePicker::make('begin_at')->nullable()->label('Begin at'),
                    DatePicker::make('end_at')->nullable()->label('End at'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('status'),
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
            ActionableControlRelationManager::class,
            ActionableObjectiveRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\DataModel\Auditing\Infrastructure\UI\ActionResource\Pages\ListActions::route('/'),
            'create' => \Swark\DataModel\Auditing\Infrastructure\UI\ActionResource\Pages\CreateAction::route('/create'),
            'edit' => \Swark\DataModel\Auditing\Infrastructure\UI\ActionResource\Pages\EditAction::route('/{record}/edit'),
        ];
    }
}
