<?php

namespace Swark\Management\Compliance\Resources;

use App\Management\Compliance\Resources;
use App\Management\Resources\Compliance\RiskResource\Pages;
use App\Management\Resources\Compliance\RiskResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Compliance\Domain\Entity\Finding;
use Swark\DataModel\Risk\Domain\Model\Strategy;
use Swark\Management\Resources\Shared;

class FindingResource extends Resource
{
    protected static ?string $model = Finding::class;

    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::scompId(),
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\Textarea::make('description'),
                Select::make('type')->options(['improvement', 'risk', 'bug']),
                Select::make('status')->options(['open', 'done']),
                Forms\Components\Textarea::make('impact'),
                Forms\Components\TextInput::make('probability'),
                Forms\Components\TextInput::make('extend_of_damage'),
                Select::make('criticality_id')->relationship('criticality', 'name')->nullable()->label('Priority'),
                Select::make('strategy')
                    ->options(Strategy::toMap()),
                Select::make('controls')
                    ->relationship('controls', 'name')
                    ->multiple(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('probability'),
                Tables\Columns\TextColumn::make('extend_of_damage'),
                Tables\Columns\TextColumn::make('strategy'),
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
            'index' => \Swark\Management\Compliance\Resources\FindingResource\Pages\ListFindings::route('/'),
            'create' => \Swark\Management\Compliance\Resources\FindingResource\Pages\CreateFinding::route('/create'),
            'edit' => \Swark\Management\Compliance\Resources\FindingResource\Pages\EditFinding::route('/{record}/edit'),
        ];
    }
}
