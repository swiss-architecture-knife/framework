<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Network;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\Vlan;
use Swark\DataModel\Presenter\UI\Business\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\VlanResource\Pages\CreateVlan;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\VlanResource\Pages\EditVlan;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\VlanResource\Pages\ListVlans;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\VlanResource\RelationManagers\BelongingIpNetworksRelationManager;
use Swark\DataModel\Presenter\UI\Shared;

class VlanResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Vlan::class;

    protected static ?string $navigationGroup = Shared::INFRASTRUCTURE;

    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->generalSection([
                Forms\Components\TextInput::make('number')->label('VLAN Port/Tag')->required(),
            ])->decorate();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('number')->label('VLAN Port/Tag'),

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
            BelongingIpNetworksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVlans::route('/'),
            'create' => CreateVlan::route('/create'),
            'edit' => EditVlan::route('/{record}/edit'),
        ];
    }
}
