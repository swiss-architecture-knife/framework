<?php

namespace Swark\Management\Architecture\Resources\Ecosystem;

use App\Management\Resources\Ecosystem\ActorResource\RelationManagers\ActorIncomingActorRelationManager;
use App\Management\Resources\Ecosystem\ActorResource\RelationManagers\ActorIncomingSystemRelationManager;
use App\Management\Resources\Ecosystem\ActorResource\RelationManagers\ActorOutgoingActorRelationManager;
use App\Management\Resources\Ecosystem\ActorResource\RelationManagers\ActorOutgoingSystemRelationManager;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Swark\DataModel\Ecosystem\Domain\Entity\Actor;
use Swark\Management\Architecture\Resources\Ecosystem\ActorResource\RelationManagers\ActorIncomingRelationManager;
use Swark\Management\Architecture\Resources\Ecosystem\ActorResource\RelationManagers\ActorOutgoingRelationManager;
use Swark\Management\Architecture\Resources\Ecosystem\ActorResource\RelationManagers\C4ArchitectureRelationManager;
use Swark\Management\Architecture\Resources\Ecosystem\ActorResource\RelationManagers\ServiceOutgoingRelationManager;
use Swark\Management\Architecture\Resources\Ecosystem\ActorResource\RelationManagers\SoftwareOutgoingRelationManager;
use Swark\Management\Architecture\Resources\Ecosystem\ActorResource\RelationManagers\SystemOutgoingRelationManager;
use Swark\Management\Architecture\Resources\Ecosystem\ActorResource\RelationManagers\ZoneRelationManager;
use Swark\Management\Resources\Shared;

class ActorResource extends Resource
{
    protected static ?string $model = Actor::class;

    protected static ?string $navigationGroup = Shared::ECOSYSTEM;

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
            'index' => \Swark\Management\Architecture\Resources\Ecosystem\ActorResource\Pages\ListActors::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Ecosystem\ActorResource\Pages\CreateActor::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Ecosystem\ActorResource\Pages\EditActor::route('/{record}/edit'),
        ];
    }
}
