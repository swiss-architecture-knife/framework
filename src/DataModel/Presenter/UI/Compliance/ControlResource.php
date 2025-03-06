<?php

namespace Swark\DataModel\Presenter\UI\Compliance;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Chapter;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Control;
use Swark\DataModel\Presenter\UI\Compliance\ControlResource\Pages\CreateControl;
use Swark\DataModel\Presenter\UI\Compliance\ControlResource\Pages\EditControl;
use Swark\DataModel\Presenter\UI\Compliance\ControlResource\Pages\ListControls;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\DataModelDecorator;

class ControlResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Control::class;

    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->generalSection([
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
            ])->decorate();
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
            'index' => ListControls::route('/'),
            'create' => CreateControl::route('/create'),
            'edit' => EditControl::route('/{record}/edit'),
        ];
    }
}
