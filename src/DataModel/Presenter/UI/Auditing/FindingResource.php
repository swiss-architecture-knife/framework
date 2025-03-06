<?php

namespace Swark\DataModel\Presenter\UI\Auditing;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Domain\Model\Auditing\FindingType;
use Swark\DataModel\Domain\Model\Auditing\Status;
use Swark\DataModel\Domain\Model\Auditing\TreatmentStrategy;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Finding;
use Swark\DataModel\Presenter\UI\Auditing\FindingResource\Pages\CreateFinding;
use Swark\DataModel\Presenter\UI\Auditing\FindingResource\Pages\EditFinding;
use Swark\DataModel\Presenter\UI\Auditing\FindingResource\Pages\ListFindings;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;

class FindingResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Finding::class;

    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->generalSection([
                Select::make('type')->options(FindingType::toMap())->required(),
                Select::make('status')->options(Status::toMap())->required(),
                Forms\Components\Textarea::make('impact'),
                Forms\Components\TextInput::make('probability'),
                Forms\Components\TextInput::make('extend_of_damage'),
                Select::make('criticality_id')->relationship('criticality', 'name')->nullable()->label('Priority'),
                Select::make('strategy')
                    ->required()
                    ->options(TreatmentStrategy::toMap()),
                Select::make('controls')
                    ->relationship('controls', 'name')
                    ->multiple(),
            ])
            ->decorate();
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
            'index' => ListFindings::route('/'),
            'create' => CreateFinding::route('/create'),
            'edit' => EditFinding::route('/{record}/edit'),
        ];
    }
}
