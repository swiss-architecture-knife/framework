<?php

namespace Swark\DataModel\Presenter\UI\SoftwareArchitecture;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Domain\Model\SoftwareArchitecture\UsageType;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Software;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\ActorIncomingRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\C4ArchitectureRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\ResourceTypeOutgoingRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\ServiceOutgoingRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\SoftwareIncomingRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\SoftwareOutgoingRelationManager;
use Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers\SystemOutgoingRelationManager;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\Shared;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\SoftwareResource\Pages\CreateSoftware;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\SoftwareResource\Pages\EditSoftware;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\SoftwareResource\Pages\ListSoftwares;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\SoftwareResource\RelationManagers\ComponentsRelationManager;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\SoftwareResource\RelationManagers\ReleasesRelationManager;
use Swark\DataModel\Presenter\UI\SoftwareArchitecture\SoftwareResource\RelationManagers\ServicesRelationManager;

class SoftwareResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Software::class;

    protected static ?string $navigationGroup = Shared::SOFTWARE;

    protected static ?int $navigationSort = 2;

    protected static ?string $label = 'Software';
    protected static ?string $navigationLabel = 'Software catalog';

    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->nameHint('Product, tool or library name')
            ->sections([
                Section::make('Vendor')->schema([
                    Select::make('vendor_id')
                        ->label('Vendor')
                        ->relationship('vendor', titleAttribute: 'name')
                        ->required(false)
                        ->nullable(),
                ]),
                Section::make('Purpose')->description('Set the purpose of this software. Depending upon it, it can be used in other elements of your architecture')->schema([
                    Select::make('usage_type')
                        ->label('Usage category')
                        ->required()
                        ->options(UsageType::toMap()),
                    Forms\Components\Checkbox::make('is_virtualizer')
                        ->default(false)
                        ->label('Virtualization software (Hypervisor etc.)')
                        ->hint('Virtualization software can be a parent of a host'),
                    Forms\Components\Checkbox::make('is_operating_system')
                        ->default(false)
                        ->label('Operating system (Windows, Linux etc.)')
                        ->hint('Operating system can run directly on hosts'),
                    Forms\Components\Checkbox::make('is_runtime')
                        ->default(false)
                        ->label('Runtime (Apache Tomcat, Websphere, Kubernetes etc.)')
                        ->hint('Runtimes provide managed execution environments for other application'),
                    Forms\Components\Checkbox::make('is_library')
                        ->default(false)
                        ->label('Library (Spring Framework, .NET Framework etc.)')
                        ->hint('Can be included by other applications'),
                    Forms\Components\Checkbox::make('is_bundle')
                        ->default(false)
                        ->label('Is bundle')
                        ->hint('Releases consists upon other releases, e.g. a Helm chart with different software versions'),
                    Select::make('artifact_type_id')
                        ->label('Default artifact type')
                        ->relationship('artifactType', 'name')
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
                Section::make('Structure')->
                description('Applications can be logically organized')
                    ->schema([
                        Select::make('logical_zone_id')
                            ->label('Assumed business zone for this application')
                            ->relationship('zone', 'name')
                        ,
                        /*
                                            Select::make('systems')
                                                ->relationship('systems', 'name')
                                                ->label('Part of the following systems')
                                                ->multiple(),
                        */
                    ])
            ])->decorate();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('vendor.name'),
                Tables\Columns\TextColumn::make('artifactType.name'),
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
            ])->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            ReleasesRelationManager::class,
            ComponentsRelationManager::class,
            ServicesRelationManager::class,
            ... C4ArchitectureRelationManager::createRelations([
                ActorIncomingRelationManager::class,
                SoftwareIncomingRelationManager::class,
            ], [
                SystemOutgoingRelationManager::class,
                SoftwareOutgoingRelationManager::class,
                ServiceOutgoingRelationManager::class,
                ResourceTypeOutgoingRelationManager::class,
            ])
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSoftwares::route('/'),
            'create' => CreateSoftware::route('/create'),
            'edit' => EditSoftware::route('/{record}/edit'),
        ];
    }
}
