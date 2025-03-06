<?php

namespace Swark\DataModel\Presenter\UI\Compliance;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Domain\Model\Compliance\RelevanceType;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Chapter;
use Swark\DataModel\Presenter\UI\Compliance\ChapterResource\Pages\CreateChapter;
use Swark\DataModel\Presenter\UI\Compliance\ChapterResource\Pages\EditChapter;
use Swark\DataModel\Presenter\UI\Compliance\ChapterResource\Pages\ListChapters;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\DataModelDecorator;

class ChapterResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Chapter::class;

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->sections([
                Section::make('Regulation')
                    ->schema([
                        Forms\Components\Select::make('regulation_id')
                            ->required()
                            ->relationship('regulation', 'Name'),
                        Forms\Components\TextInput::make('external_id')->label('Official regulation chapter number'),
                        Forms\Components\TextInput::make('name')->label('Heading'),
                        Forms\Components\MarkdownEditor::make('summary'),
                        Forms\Components\MarkdownEditor::make('official_content'),
                    ]),
                Section::make('Context')->schema([
                    Forms\Components\Select::make('relevancy')->options(RelevanceType::toMap())->nullable()->label('Relevance for us'),
                    Forms\Components\MarkdownEditor::make('actual_status')->label('Actual status')->nullable(),
                    Forms\Components\MarkdownEditor::make('target_status')->label('Target status')->nullable(),
                ])
            ])
            ->decorate();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('regulation.name'),
                Tables\Columns\TextColumn::make('external_id')->label('Chapter number'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('relevancy')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'high' => 'danger',
                        'middle' => 'warning',
                        'low' => 'primary',
                        default => 'gray',
                    })
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
            'index' => ListChapters::route('/'),
            'create' => CreateChapter::route('/create'),
            'edit' => EditChapter::route('/{record}/edit'),
        ];
    }
}
