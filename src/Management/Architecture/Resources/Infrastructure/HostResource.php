<?php

namespace Swark\Management\Architecture\Resources\Infrastructure;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Illuminate\Database\Eloquent\Builder;
use Swark\DataModel\Infrastructure\Domain\Entity\Host;
use Swark\Management\Architecture\Resources\Ecosystem\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\Management\Architecture\Resources\Infrastructure\BaremetalResource\Pages\EditBaremetal;
use Swark\Management\Architecture\Resources\Infrastructure\HostResource\Pages\CreateHostNic;
use Swark\Management\Architecture\Resources\Infrastructure\HostResource\Pages\ManageHostNics;
use Swark\Management\Resources\Shared;

class HostResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Host::class;

    protected static ?string $navigationGroup = Shared::INFRASTRUCTURE;

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(),
                Section::make('Underlying hardware or software')->schema([

                    Select::make('baremetal_id')
                        ->relationship('baremetal', 'name'),
                    Select::make('parent_host_id')
                        ->hint('Only parent hosts with virtualization options are available')
                        ->relationship('parentHost', titleAttribute: 'name',
                            modifyQueryUsing: fn(Builder $query) => $query->whereNotNull('virtualizer_id'),
                            ignoreRecord: true,
                        ),
                ]),
                Section::make('Provided software')->schema([
                    Select::make('operating_system_id')
                        ->hint('Only applications with operating system option and at least one version are shown')
                        ->relationship('operatingSystem', 'software.name',
                            modifyQueryUsing: fn(Builder $query) => $query
                                ->leftJoin('software', 'software.id', 'release.software_id')
                                ->select(['release.id', 'release.version', 'software.name'])
                                ->where('software.is_operating_system', true)
                                ->orderBy('software.name')->orderBy('release.version')
                        )
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} > {$record->version}")
                        ->required(),
                    Select::make('virtualizer_id')
                        ->hint('Only applications with virtualization option and at least one version are shown')
                        ->relationship('virtualizer', 'software.name',
                            modifyQueryUsing: fn(Builder $query) => $query
                                ->leftJoin('software', 'software.id', 'release.software_id')
                                ->select(['release.id', 'release.version', 'software.name'])
                                ->where('software.is_virtualizer', true)
                                ->orderBy('software.name')->orderBy('release.version')
                        )
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} > {$record->version}"),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Hostname'),
                Tables\Columns\TextColumn::make('operatingSystem.software.name')->label('Operating system'),
                Tables\Columns\TextColumn::make('baremetal.name')->label('Baremetal'),
                Tables\Columns\TextColumn::make('parentHost.name')->label('Virtualized on'),
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
            AssociatedWithOrganizationsRelationManager::class,
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EditBaremetal::class,
            ManageHostNics::class
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\Management\Architecture\Resources\Infrastructure\HostResource\Pages\ListHosts::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Infrastructure\HostResource\Pages\CreateHost::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Infrastructure\HostResource\Pages\EditHost::route('/{record}/edit'),
            'nics' => ManageHostNics::route('/{record}/nics'),
            'nics.create' => CreateHostNic::route('/{record}/nics/create'),
        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }
}

