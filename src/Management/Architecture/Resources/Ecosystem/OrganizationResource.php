<?php

namespace Swark\Management\Architecture\Resources\Ecosystem;

use App\Management\Resources\Actor\OrganizationResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Swark\DataModel\Ecosystem\Domain\Entity\Organization;
use Swark\DataModel\Ecosystem\Domain\Model\ImportanceType;
use Swark\Management\Architecture\Resources\Ecosystem\OrganizationResource\RelationManagers\AccountsRelationManager;
use Swark\Management\Resources\Shared;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationGroup = Shared::ECOSYSTEM;

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(),
                Section::make('Type of organization')
                    ->description('Each organization can be a vendor, customer or service provider or all of them')->schema([
                        Forms\Components\Checkbox::make('is_internal')->default(false)->hint('Enable this checkbox if this is your company or internal organization'),
                        Forms\Components\Checkbox::make('is_vendor')->default(true)->hint('Vendors can be assigned to software'),
                        Forms\Components\Checkbox::make('is_customer')->default(false)->hint('Customers can be assigned for using a resource'),
                        Forms\Components\Checkbox::make('is_managed_service_provider')->default(false)->hint('A service provider is an organization, providing you with managed resources, e.g. AWS or Hetzner'),
                    ]),
                Section::make('Regulation')->schema([
                    Forms\Components\Select::make('importance')->options(ImportanceType::toMap())->nullable()->hint('Define the importance of this organization. Depending upon the importance, different regulations may apply e.g. for companies subject to NIS2 or KRITIS'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Organization'),
                CheckboxColumn::make('is_vendor'),
                CheckboxColumn::make('is_customer'),
                CheckboxColumn::make('is_managed_service_provider'),
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
            AccountsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\Management\Architecture\Resources\Ecosystem\OrganizationResource\Pages\ListOrganizations::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Ecosystem\OrganizationResource\Pages\CreateOrganization::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Ecosystem\OrganizationResource\Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
