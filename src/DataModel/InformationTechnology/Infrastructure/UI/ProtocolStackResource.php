<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Swark\DataModel\Governance\Domain\Entity\TechnologyVersion;
use Swark\DataModel\Governance\Domain\Model\TechnologyType;
use Swark\DataModel\InformationTechnology\Domain\Entity\ProtocolStack;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class ProtocolStackResource extends Resource
{
    protected static ?string $model = ProtocolStack::class;

    protected static ?string $navigationGroup = Shared::ECOSYSTEM;

    protected static ?int $navigationSort = 7;

    public static function selectTechnology(string $name, string $label, string $relationship, bool $allowDataFormats = false): Select
    {
        return Forms\Components\Select::make($name)->label($label)
            ->relationship($relationship, titleAttribute: 'name', modifyQueryUsing: function (Builder $query) use ($allowDataFormats) {
                $query = $query->leftJoin('technology', 'technology.id', '=', 'technology_version.technology_id')
                    ->select('technology_version.id', 'technology.name as name', 'technology_version.name as version');
                $allowedTechnologyTypes = [TechnologyType::PROTOCOL];
                if ($allowDataFormats) {
                    $allowedTechnologyTypes[] = TechnologyType::DATA_FORMAT;
                }
                $query = $query->whereIn('technology.type', $allowedTechnologyTypes);
            })
            ->getOptionLabelFromRecordUsing(fn(TechnologyVersion $record) => "{$record->name}:{$record->version}")
            ->searchable(['technology.name', 'technology_version.name']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(withScompId: false),
                Forms\Components\TextInput::make('port')->label('Default communication port (TCP/UDP)')->nullable(),
                Forms\Components\Section::make('Protocols for each layer of the stack')->description('If you do not need the full OSI stack description, just select the protocol for the application layer')
                    ->schema(([
                        static::selectTechnology('application_layer_id', 'Application', 'applicationLayer', allowDataFormats: true)->required(),
                        static::selectTechnology('presentation_layer_id', 'Presentation', 'presentationLayer'),
                        static::selectTechnology('session_layer_id', 'Session', 'sessionLayer'),
                        static::selectTechnology('transport_layer_id', 'Transport', 'transportLayer'),
                        static::selectTechnology('network_layer_id', 'Network', 'networkLayer'),
                    ]))
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Protocol stack'),
                Tables\Columns\TextColumn::make('applicationLayer.technology.name')->label('Application layer'),
                Tables\Columns\TextColumn::make('transportLayer.technology.name')->label('Transport layer'),
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
            'index' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\ProtocolStackResource\Pages\ListProtocolStacks::route('/'),
            'create' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\ProtocolStackResource\Pages\CreateProtocolStack::route('/create'),
            'edit' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\ProtocolStackResource\Pages\EditProtocolStack::route('/{record}/edit'),
        ];
    }
}
