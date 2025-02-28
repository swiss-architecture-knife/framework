<?php

namespace Swark\DataModel\Compliance\Infrastructure\UI;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Compliance\Domain\Entity\Chapter;
use Swark\DataModel\Compliance\Domain\Model\RelevanceType;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class ChapterResource extends Resource
{
    protected static ?string $model = Chapter::class;

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Regulation')
                    ->schema([
                        Forms\Components\Select::make('regulation_id')->relationship('regulation', 'Name'),
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
            ]);
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
            'index' => \Swark\DataModel\Compliance\Infrastructure\UI\ChapterResource\Pages\ListChapters::route('/'),
            'create' => \Swark\DataModel\Compliance\Infrastructure\UI\ChapterResource\Pages\CreateChapter::route('/create'),
            'edit' => \Swark\DataModel\Compliance\Infrastructure\UI\ChapterResource\Pages\EditChapter::route('/{record}/edit'),
        ];
    }
}
