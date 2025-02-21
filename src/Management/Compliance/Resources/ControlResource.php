<?php

namespace Swark\Management\Compliance\Resources;

use App\Management\Compliance\Resources;
use App\Management\Resources\Compliance\ControlResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Swark\DataModel\Compliance\Domain\Entity\Chapter;
use Swark\DataModel\Compliance\Domain\Entity\Control;
use Swark\Management\Resources\Shared;

class ControlResource extends Resource
{
    protected static ?string $model = Control::class;

    protected static ?string $navigationGroup = Shared::COMPLIANCE;
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
                Select::make('risks')
                    ->relationship('risks', 'name')
                    ->multiple(),
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
            'index' => \Swark\Management\Compliance\Resources\ControlResource\Pages\ListControls::route('/'),
            'create' => \Swark\Management\Compliance\Resources\ControlResource\Pages\CreateControl::route('/create'),
            'edit' => \Swark\Management\Compliance\Resources\ControlResource\Pages\EditControl::route('/{record}/edit'),
        ];
    }
}
