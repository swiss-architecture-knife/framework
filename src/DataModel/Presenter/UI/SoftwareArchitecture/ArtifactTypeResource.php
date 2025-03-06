<?php

namespace Swark\DataModel\Presenter\UI\SoftwareArchitecture;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\ArtifactType;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\Shared;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\ArtifactTypeResource\Pages\CreateArtifactType;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\ArtifactTypeResource\Pages\EditArtifactType;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\ArtifactTypeResource\Pages\ListArtifactTypes;

class ArtifactTypeResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = ArtifactType::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = Shared::ECOSYSTEM;

    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->generalDescription('Add artifact types you are using, e.g. Docker files, RPMs and so on')
            ->decorate();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Artifact type')
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
            'index' => ListArtifactTypes::route('/'),
            'create' => CreateArtifactType::route('/create'),
            'edit' => EditArtifactType::route('/{record}/edit'),
        ];
    }
}
