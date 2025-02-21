<?php

namespace Swark\Management\Architecture\Resources\Software\SoftwareResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;
use Swark\DataModel\Software\Domain\Entity\Component;
use Swark\Management\Resources\Shared;

class ServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'services';

    protected static ?string $title = 'Provided services';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General')
                    ->description("Services can published by your software's components. Other application instances or cluster can subscribe to them.")
                    ->
                    schema([
                        Shared::requiredName(enableUnique: false)->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                            $rule->where('component_id', $get('component'));
                        })->label('Service name'),
                        Forms\Components\Select::make('component_id')
                            ->label("Application's component")
                            ->relationship('component', modifyQueryUsing: function (Builder $query, RelationManager $livewire) {
                                $query->where('software_id', $livewire->getOwnerRecord()->id);
                            })
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn(Component $record) => "{$record->name}")
                        ,
                        Forms\Components\Textarea::make('description')->nullable(),
                        Forms\Components\Select::make('protocolStacks')
                            ->label('Usable with protocol stacks')
                            ->relationship(name: 'protocolStacks', titleAttribute: 'name'
                            )
                            ->multiple()
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Service'),
                TextColumn::make('component.name')->label('Provided by component'),
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
