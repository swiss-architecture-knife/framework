<?php

namespace Swark\DataModel\Presenter\UI\Meta;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Domain\Model\Governance\TechnologyVersionName;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\ResourceType;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\C4ArchitectureRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\ComponentIncomingRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\ServiceIncomingRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\SoftwareIncomingRelationManager;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\Meta\ResourceTypeResource\Pages\CreateResourceType;
use Swark\DataModel\Presenter\UI\Meta\ResourceTypeResource\Pages\EditResourceType;
use Swark\DataModel\Presenter\UI\Meta\ResourceTypeResource\Pages\ListResourceTypes;
use Swark\DataModel\Presenter\UI\Shared;

class ResourceTypeResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = ResourceType::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = Shared::ECOSYSTEM;

    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->sections([
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
            ])
            ->decorate();
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
            'index' => ListResourceTypes::route('/'),
            'create' => CreateResourceType::route('/create'),
            'edit' => EditResourceType::route('/{record}/edit'),
        ];
    }
}
