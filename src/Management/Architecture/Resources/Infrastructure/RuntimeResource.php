<?php

namespace Swark\Management\Architecture\Resources\Infrastructure;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Swark\DataModel\Infrastructure\Domain\Entity\Runtime;
use Swark\Management\Resources\Shared;

class RuntimeResource extends Resource
{
    protected static ?string $model = Runtime::class;

    protected static ?int $navigationSort = 12;

    protected static ?string $navigationGroup = Shared::INFRASTRUCTURE;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(),
                Section::make('Parent host')->schema([
                    Forms\Components\Select::make('host_id')
                        ->label('Runtime runs on host')
                        ->relationship('host', 'name'),
                ]),
                Section::make('Runtime environment')->schema([
                    Select::make('release_id')
                        ->hint('Only softwares with runtime option and at least one version are shown')
                        ->relationship('release', 'software.name',
                            modifyQueryUsing: fn(Builder $query) => $query
                                ->leftJoin('software', 'software.id', 'release.software_id')
                                ->select(['release.id', 'release.version', 'software.name'])
                                ->where('software.is_runtime', true)
                                ->orderBy('software.name')
                                ->orderBy('release.version')
                        )
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} > {$record->version}"),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('host.name'),
                Tables\Columns\TextColumn::make('softwareVersion.software.name')
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
            'index' => \Swark\Management\Architecture\Resources\Infrastructure\RuntimeResource\Pages\ListRuntimes::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Infrastructure\RuntimeResource\Pages\CreateRuntime::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Infrastructure\RuntimeResource\Pages\EditRuntime::route('/{record}/edit'),
        ];
    }
}
