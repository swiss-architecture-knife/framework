<?php

namespace Swark\Management\Compliance\Resources;

use App\Management\Resources\Compliance\ActionResource\Pages;
use App\Management\Resources\Compliance\ActionResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Action\Domain\Entity\Action;
use Swark\DataModel\Action\Domain\Model\Status;
use Swark\Management\Compliance\Resources\ActionResource\RelationManagers\ActionableControlRelationManager;
use Swark\Management\Compliance\Resources\ActionResource\RelationManagers\ActionableObjectiveRelationManager;
use Swark\Management\Resources\Shared;

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
                    Select::make('status')->options(Status::toMap())->nullable()->label('Status'),
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
            'index' => \Swark\Management\Compliance\Resources\ActionResource\Pages\ListActions::route('/'),
            'create' => \Swark\Management\Compliance\Resources\ActionResource\Pages\CreateAction::route('/create'),
            'edit' => \Swark\Management\Compliance\Resources\ActionResource\Pages\EditAction::route('/{record}/edit'),
        ];
    }
}
