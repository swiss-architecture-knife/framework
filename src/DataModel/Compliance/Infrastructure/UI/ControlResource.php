<?php

namespace Swark\DataModel\Compliance\Infrastructure\UI;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Swark\DataModel\Compliance\Domain\Entity\Chapter;
use Swark\DataModel\Compliance\Domain\Entity\Control;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class ControlResource extends Resource
{
    protected static ?string $model = Control::class;

    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('external_id'),
                Forms\Components\Textarea::make('content'),
                Forms\Components\Select::make('regulation_id')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->relationship('regulation', 'name')->required(),
                Forms\Components\Select::make('chapter')
                    ->relationship('chapter', 'name')
                    ->options(fn(Forms\Get $get): Collection => Chapter::query()->where('regulation_id', $get('regulation_id'))->pluck('name', 'id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('regulation.name'),
                Tables\Columns\TextColumn::make('chapter.name'),
                Tables\Columns\TextColumn::make('name'),
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
            'index' => \Swark\DataModel\Compliance\Infrastructure\UI\ControlResource\Pages\ListControls::route('/'),
            'create' => \Swark\DataModel\Compliance\Infrastructure\UI\ControlResource\Pages\CreateControl::route('/create'),
            'edit' => \Swark\DataModel\Compliance\Infrastructure\UI\ControlResource\Pages\EditControl::route('/{record}/edit'),
        ];
    }
}
