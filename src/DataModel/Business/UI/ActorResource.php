<?php

namespace Swark\DataModel\Business\UI;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Swark\DataModel\Business\Domain\Entity\Actor;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\ActorIncomingRelationManager;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\ActorOutgoingRelationManager;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\C4ArchitectureRelationManager;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\ServiceOutgoingRelationManager;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\SoftwareOutgoingRelationManager;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\SystemOutgoingRelationManager;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\ZoneRelationManager;
use Swark\Management\Resources\Shared;

class ActorResource extends Resource
{
    protected static ?string $model = Actor::class;

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(clazz: self::$model),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
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

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [
            ZoneRelationManager::class,
            ... C4ArchitectureRelationManager::createRelations([
                ActorIncomingRelationManager::class,
            ], [
                ActorOutgoingRelationManager::class,
                SystemOutgoingRelationManager::class,
                SoftwareOutgoingRelationManager::class,
                ServiceOutgoingRelationManager::class,
            ])
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\DataModel\Business\UI\ActorResource\Pages\ListActors::route('/'),
            'create' => \Swark\DataModel\Business\UI\ActorResource\Pages\CreateActor::route('/create'),
            'edit' => \Swark\DataModel\Business\UI\ActorResource\Pages\EditActor::route('/{record}/edit'),
        ];
    }
}
