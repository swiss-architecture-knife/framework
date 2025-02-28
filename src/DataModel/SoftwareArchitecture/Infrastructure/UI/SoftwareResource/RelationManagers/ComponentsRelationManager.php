<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\SoftwareResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;
use Swark\DataModel\Governance\Domain\Entity\TechnologyVersion;
use Swark\DataModel\Governance\Domain\Model\TechnologyVersionName;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;
use Swark\DataModel\Meta\Domain\Model\ProviderConsumerType;

class ComponentsRelationManager extends RelationManager
{
    protected static string $relationship = 'components';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General')->schema([
                    Shared::requiredName(enableUnique: false)->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                        $softwareId = $this->getOwnerRecord()->id;
                        return $rule->where('software_id', $softwareId);
                    }),
                    Forms\Components\Textarea::make('description')->nullable(),
                ]),
                Section::make('Technical')->schema([
                    Forms\Components\Select::make('technologies_consuming')
                        ->label('Used technologies by this component')
                        ->relationship('technologiesConsuming', titleAttribute: 'name', modifyQueryUsing: fn(Builder $query) => $query->leftJoin('technology', 'technology.id', '=', 'technology_version.technology_id')
                            ->where(function (Builder $query) {
                                $query->where('provider_consumer_type', ProviderConsumerType::CONSUMER)
                                    ->orWhereNull('provider_consumer_type');
                            })

                        )
                        ->getOptionLabelFromRecordUsing(fn(TechnologyVersion $record) => TechnologyVersionName::from($record)->title())
                        ->pivotData(['provider_consumer_type' => ProviderConsumerType::CONSUMER])
                        ->searchable(['technology.name', 'technology_version.name'])
                        ->multiple(),
                    Forms\Components\Select::make('technologies_providing')
                        ->label('Provided technologies by this component')
                        ->relationship('technologiesProducing', titleAttribute: 'name', modifyQueryUsing: fn(Builder $query) => $query->leftJoin('technology', 'technology.id', '=', 'technology_version.technology_id')
                            ->where(function (Builder $query) {
                                $query->where('provider_consumer_type', ProviderConsumerType::PROVIDER)
                                    ->orWhereNull('provider_consumer_type');
                            })
                        )
                        ->getOptionLabelFromRecordUsing(fn(TechnologyVersion $record) => TechnologyVersionName::from($record)->title())
                        ->pivotData(['provider_consumer_type' => ProviderConsumerType::PROVIDER])
                        ->searchable(['technology.name', 'technology_version.name'])
                        ->multiple(),
                    Forms\Components\Select::make('layers')->label('Assigned layers')
                        ->relationship('layers', titleAttribute: 'name')
                        ->multiple(),
                ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Component'),
                TextColumn::make('technologies_count')->counts('technologies')->label('Connected technologies'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
