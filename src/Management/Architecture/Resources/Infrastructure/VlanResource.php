<?php

namespace Swark\Management\Architecture\Resources\Infrastructure;

use App\Management\Resources\Infrastructure\BaremetalResource\Pages;
use App\Management\Resources\Infrastructure\BaremetalResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Business\UI\OrganizationResource\RelationManagers\AssociatedWithOrganizationsRelationManager;
use Swark\DataModel\Network\Domain\Entity\Vlan;
use Swark\Management\Architecture\Resources\Infrastructure\VlanResource\RelationManagers\BelongingIpNetworksRelationManager;
use Swark\Management\Resources\Shared;

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
            'index' => \Swark\Management\Architecture\Resources\Infrastructure\VlanResource\Pages\ListVlans::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Infrastructure\VlanResource\Pages\CreateVlan::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Infrastructure\VlanResource\Pages\EditVlan::route('/{record}/edit'),
        ];
    }
}
