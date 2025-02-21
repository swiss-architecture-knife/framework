<?php

namespace Swark\Management\Architecture\Resources\Deployment;

use App\Management\Resources\Deployment\ApplicationInstanceResource\Pages;
use App\Management\Resources\Deployment\ApplicationInstanceResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Swark\DataModel\Deployment\Domain\Entity\ApplicationInstance;
use Swark\DataModel\Infrastructure\Domain\Entity\Host;
use Swark\DataModel\Infrastructure\Domain\Entity\Runtime;
use Swark\Management\Architecture\Resources\Ecosystem\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\Management\Architecture\Resources\Infrastructure\ClusterResource\RelationManagers\ResourcesRelationManager;
use Swark\Management\Architecture\Resources\Infrastructure\IpNetworkResource\RelationManagers\AssignableIpNetworkRelationManager;
use Swark\Management\Architecture\Resources\Infrastructure\IpNetworkResource\RelationManagers\IpAddressAssignedRelationManager;
use Swark\Management\Resources\Shared;

class ApplicationInstanceResource extends Resource
{
    protected static ?string $model = ApplicationInstance::class;

    protected static ?string $navigationGroup = Shared::DEPLOYMENT;

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Deployment')->schema([
                    Select::make('release_id')
                        ->label('Software release')
                        ->relationship(
                            'release',
                            titleAttribute: 'software.name',
                            // TODO: Dependent filter for softwareVersion so that only parent version can be selected
                            // @see https://v2.filamentphp.com/tricks/dependent-select-filters
                            // we have to join the parent software's name to it, otherwise release.id gets mixed up with software.id and only one version is shown
                            modifyQueryUsing: fn(Builder $query) => $query
                                ->leftJoin('software', 'software.id', 'release.software_id')
                                ->select(['release.id', 'release.version', 'software.name'])
                        )
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} > {$record->version}")
                        ->required(),
                    Select::make('deployment')
                        ->label('Part of deployment')
                        ->relationship('deployment', titleAttribute: 'name')
                        ->required(false),
                ]),
                Section::make('Structure')->
                description('Applications can be logically organized')
                    ->schema([
                        Select::make('system_id')
                            ->label('Parent system')
                            ->relationship('system', 'name')
                            ->nullable(),
                        Select::make('logical_zone_id')
                            ->label('Assumed business zone for this application')
                            ->relationship('zone', 'name')
                        ,
                        Forms\Components\Select::make('stage')
                            ->relationship('stage', 'name'),
                    ]),
                Forms\Components\Section::make('Runtime environment')->schema([
                    Forms\Components\MorphToSelect::make('executor')->label('Execution on')->types([
                        Forms\Components\MorphToSelect\Type::make(Runtime::class)->titleAttribute('name'),
                        Forms\Components\MorphToSelect\Type::make(Host::class)->titleAttribute('name'),
                    ])->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('executor.name')->label('Execution on'),
                Tables\Columns\TextColumn::make('release.software.name'),
                Tables\Columns\TextColumn::make('release.version'),
                Tables\Columns\TextColumn::make('stage.name'),
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
            AssignableIpNetworkRelationManager::class,
            IpAddressAssignedRelationManager::class,
            AssociatedWithOrganizationsRelationManager::class,
            ResourcesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\Management\Architecture\Resources\Deployment\ApplicationInstanceResource\Pages\ListApplicationInstances::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Deployment\ApplicationInstanceResource\Pages\CreateApplicationInstance::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Deployment\ApplicationInstanceResource\Pages\EditApplicationInstance::route('/{record}/edit'),
        ];
    }
}
