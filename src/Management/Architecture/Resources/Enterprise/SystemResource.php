<?php

namespace Swark\Management\Architecture\Resources\Enterprise;

use App\Management\Resources\Enterprise\SystemResource\Pages;
use App\Management\Resources\Enterprise\SystemResource\RelationManagers;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\ActorIncomingRelationManager;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\C4ArchitectureRelationManager;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\ServiceOutgoingRelationManager;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\SoftwareIncomingRelationManager;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\SystemIncomingRelationManager;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\SystemOutgoingRelationManager;
use Swark\DataModel\Enterprise\Domain\Entity\System;
use Swark\Management\Resources\Shared;

class SystemResource extends Resource
{
    protected static ?string $model = System::class;

    protected static ?string $navigationGroup = Shared::ENTERPRISE_ARCHITECTURE;

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(clazz: self::$model),
                Section::make('Environment')->schema([
                    Select::make('zone')
                        ->label('System runs in logical zone')
                        ->relationship('zone', titleAttribute: 'name'),
                    Select::make('stage')
                        ->label('Stage')
                        ->relationship('stage', titleAttribute: 'name'),
                ]),
                Section::make('Criticality')->schema([
                    Select::make('business_criticality_id')
                        ->relationship('businessCriticality', 'name')
                        ->label('For business continuity'),
                    Select::make('infrastructure_criticality_id')
                        ->relationship('infrastructureCriticality', 'name')
                        ->label('For infrastructure')
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('System'),
                Tables\Columns\TextColumn::make('description')->label('Description'),
                Tables\Columns\TextColumn::make('releasetrains_count')->counts('releasetrains')
                    ->label('In release trains')
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
            \Swark\Management\Architecture\Resources\Enterprise\SystemResource\RelationManagers\ProtectionGoalRelationManager::class,
            \Swark\Management\Architecture\Resources\Enterprise\SystemResource\RelationManagers\SystemParameterRelationManager::class,
            \Swark\Management\Architecture\Resources\Enterprise\SystemResource\RelationManagers\ApplicationInstanceRelationManager::class,
            \Swark\Management\Architecture\Resources\Enterprise\SystemResource\RelationManagers\SoftwareForSystemRelationManager::class,
            \Swark\Management\Architecture\Resources\Enterprise\SystemResource\RelationManagers\ResourceTypeForSystemRelationManager::class,
            ... C4ArchitectureRelationManager::createRelations([
                ActorIncomingRelationManager::class,
                SystemIncomingRelationManager::class,
                SoftwareIncomingRelationManager::class,
            ], [
                SystemOutgoingRelationManager::class,
                ServiceOutgoingRelationManager::class,
            ])
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\Management\Architecture\Resources\Enterprise\SystemResource\Pages\ListSystems::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Enterprise\SystemResource\Pages\CreateSystem::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Enterprise\SystemResource\Pages\EditSystem::route('/{record}/edit'),
        ];
    }
}
