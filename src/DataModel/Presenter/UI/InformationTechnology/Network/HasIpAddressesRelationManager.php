<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Network;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class HasIpAddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'ipAddresses';

    protected static ?string $title = "IP addresses";
    protected static ?string $inverseRelationship = 'ipNetwork';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('recordId')->default(function (RelationManager $livewire): int {
                return $livewire->getOwnerRecord()->id;
            }),
            TextInput::make('address')->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('address')
            ->columns([
                Tables\Columns\TextColumn::make('address'),
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->inverseRelationship('ipNetwork');
    }
}
