<?php

namespace Swark\Management\Architecture\Resources\Software;

use App\Management\Resources\Software\ArtifactTypeResource\Pages;
use App\Management\Resources\Software\ArtifactTypeResource\RelationManagers;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Software\Domain\Entity\ArtifactType;
use Swark\Management\Resources\Shared;

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
            'index' => \Swark\Management\Architecture\Resources\Software\ArtifactTypeResource\Pages\ListArtifactTypes::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Software\ArtifactTypeResource\Pages\CreateArtifactType::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Software\ArtifactTypeResource\Pages\EditArtifactType::route('/{record}/edit'),
        ];
    }
}
