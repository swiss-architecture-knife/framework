<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Business\Infrastructure\UI\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\Vlan;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\VlanResource\RelationManagers\BelongingIpNetworksRelationManager;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class VlanResource extends Resource
{
    protected static ?string $model = Vlan::class;

    protected static ?string $navigationGroup = Shared::INFRASTRUCTURE;

    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General')->schema([
                    Shared::scompId()->label('Scomp-ID')->hint('A Scomp-ID can be used for retrieving elements through a hierarchy'),
                    Forms\Components\TextInput::make('number')->label('VLAN Port/Tag')->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('scomp_id'),
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
            'index' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\VlanResource\Pages\ListVlans::route('/'),
            'create' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\VlanResource\Pages\CreateVlan::route('/create'),
            'edit' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\VlanResource\Pages\EditVlan::route('/{record}/edit'),
        ];
    }
}
