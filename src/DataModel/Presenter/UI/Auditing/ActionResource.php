<?php

namespace Swark\DataModel\Presenter\UI\Auditing;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Domain\Model\Auditing\Status;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Action;
use Swark\DataModel\Presenter\UI\Auditing\ActionResource\Pages\CreateAction;
use Swark\DataModel\Presenter\UI\Auditing\ActionResource\Pages\EditAction;
use Swark\DataModel\Presenter\UI\Auditing\ActionResource\Pages\ListActions;
use Swark\DataModel\Presenter\UI\Auditing\ActionResource\RelationManagers\ActionableControlRelationManager;
use Swark\DataModel\Presenter\UI\Auditing\ActionResource\RelationManagers\ActionableObjectiveRelationManager;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\Shared;

class ActionResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Action::class;

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->nameHint('Summary of what to do')
            ->generalSection([
                Forms\Components\MarkdownEditor::make('description'),
            ])
            ->sections([
                Forms\Components\Section::make('Tracing')->schema([
                    Select::make('status')
                        ->options(Status::toMap())
                        ->label('Status')
                        ->required(),
                    DatePicker::make('begin_at')->nullable()->label('Begin at'),
                    DatePicker::make('end_at')->nullable()->label('End at'),
                ])
            ])
            ->decorate();
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
            'index' => ListActions::route('/'),
            'create' => CreateAction::route('/create'),
            'edit' => EditAction::route('/{record}/edit'),
        ];
    }
}
