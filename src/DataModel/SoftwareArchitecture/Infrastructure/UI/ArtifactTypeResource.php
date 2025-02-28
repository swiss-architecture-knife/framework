<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\ArtifactType;

class ArtifactTypeResource extends Resource
{
    protected static ?string $model = ArtifactType::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = Shared::ECOSYSTEM;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(description: 'Add artifact types you are using, e.g. Docker files, RPMs and so on')
            ]);
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
            'index' => \Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\ArtifactTypeResource\Pages\ListArtifactTypes::route('/'),
            'create' => \Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\ArtifactTypeResource\Pages\CreateArtifactType::route('/create'),
            'edit' => \Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\ArtifactTypeResource\Pages\EditArtifactType::route('/{record}/edit'),
        ];
    }
}
