<?php

namespace Swark\DataModel\Presenter\UI\Business;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Actor;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\ActorIncomingRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\ActorOutgoingRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\C4ArchitectureRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\ServiceOutgoingRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\SoftwareOutgoingRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\SystemOutgoingRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\ZoneRelationManager;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\DataModelDecorator;
use Swark\DataModel\Presenter\UI\Shared;

class ActorResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Actor::class;

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return self::decorator($form)->decorate();
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
            'index' => \Swark\DataModel\Presenter\UI\Business\ActorResource\Pages\ListActors::route('/'),
            'create' => \Swark\DataModel\Presenter\UI\Business\ActorResource\Pages\CreateActor::route('/create'),
            'edit' => \Swark\DataModel\Presenter\UI\Business\ActorResource\Pages\EditActor::route('/{record}/edit'),
        ];
    }
}
