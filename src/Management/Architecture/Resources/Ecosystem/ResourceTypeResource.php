<?php

namespace Swark\Management\Architecture\Resources\Ecosystem;

use App\Management\Resources\Ecosystem\ResourceTypeResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\C4ArchitectureRelationManager;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\ComponentIncomingRelationManager;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\ServiceIncomingRelationManager;
use Swark\DataModel\Business\UI\ActorResource\RelationManagers\SoftwareIncomingRelationManager;
use Swark\DataModel\Ecosystem\Domain\Entity\ResourceType;
use Swark\DataModel\Ecosystem\Domain\Model\TechnologyVersionName;
use Swark\Management\Resources\Shared;

class ResourceTypeResource extends Resource
{
    protected static ?string $model = ResourceType::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = Shared::ECOSYSTEM;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(),
                Forms\Components\Section::make('Referenced technology')->schema([

                    Forms\Components\Select::make('technology_version_id')
                        ->label('Technology in version')
                        ->relationship('technologyVersion',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn(Builder $query) => $query
                                ->leftJoin('technology', 'technology.id', '=', 'technology_version.technology_id')
                                ->select([
                                    'technology_version.id',
                                    'technology_version.name as technology_version_name',
                                    'technology.name AS technology_name'
                                ])
                        )
                        ->getOptionLabelFromRecordUsing(fn(Model $record) => TechnologyVersionName::from($record))
                        ->searchable(['technology.name', 'technology_version.name'])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Resource type'),
                Tables\Columns\TextColumn::make('technologyVersion')
                    ->label('Referenced technology')->formatStateUsing(fn(string $state, Model $record) => TechnologyVersionName::from($record->technologyVersion)),
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
            ... C4ArchitectureRelationManager::createRelations(incoming: [
                SoftwareIncomingRelationManager::class,
                ComponentIncomingRelationManager::class,
                ServiceIncomingRelationManager::class,
            ])
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\Management\Architecture\Resources\Ecosystem\ResourceTypeResource\Pages\ListResourceTypes::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Ecosystem\ResourceTypeResource\Pages\CreateResourceType::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Ecosystem\ResourceTypeResource\Pages\EditResourceType::route('/{record}/edit'),
        ];
    }
}
