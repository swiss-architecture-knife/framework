<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\SoftwareResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ReleasesRelationManager extends RelationManager
{
    protected static string $relationship = 'releases';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('version')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_latest')
                    ->label('Is latest release')
                    ->hint('If checked, this release is selected by default'),
                Forms\Components\Toggle::make('is_any')->label('Matches any release')
                    ->hint('If checked, this release can be referenced as a wildcard so ayn release of this software is allowed')
                ,
                Section::make('Changelog')->schema([
                    Forms\Components\MarkdownEditor::make('changelog')->nullable(),
                    Forms\Components\TextInput::make('changelog_url')->label('Changelog URL')
                        ->hint('Alternatively, URL to changelog')
                        ->nullable(),
                ]),
                // TODO Multiselect for bundled software if parent.is_bundle = true

                // kind of hacky: as this is a child of Software, we have to retrieve the MorphTo parent field of the child model
                // do not forget to add software_id to the list of $fillable attributes in Version model
                Forms\Components\Hidden::make('software_id')->default(function (RelationManager $livewire): int {
                    return $livewire->getOwnerRecord()->id;
                }),
            ]);

    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('version')
            ->columns([
                Tables\Columns\TextColumn::make('version'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
