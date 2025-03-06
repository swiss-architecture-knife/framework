<?php

namespace Swark\DataModel\Presenter\UI\Business;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Swark\DataModel\Domain\Model\Business\DependencyDegree;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Organization;
use Swark\DataModel\Presenter\UI\Business\OrganizationResource\Pages\CreateOrganization;
use Swark\DataModel\Presenter\UI\Business\OrganizationResource\Pages\EditOrganization;
use Swark\DataModel\Presenter\UI\Business\OrganizationResource\Pages\ListOrganizations;
use Swark\DataModel\Presenter\UI\Business\OrganizationResource\RelationManagers\AccountsRelationManager;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\DataModelDecorator;

class OrganizationResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Organization::class;

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->sections([
                Section::make('Type of organization')
                    ->description('Each organization can be a vendor, customer or service provider or all of them')->schema([
                        Forms\Components\Checkbox::make('is_internal')->default(false)->hint('Enable this checkbox if this is your company or internal organization'),
                        Forms\Components\Checkbox::make('is_vendor')->default(true)->hint('Vendors can be assigned to software'),
                        Forms\Components\Checkbox::make('is_customer')->default(false)->hint('Customers can be assigned for using a resource'),
                        Forms\Components\Checkbox::make('is_managed_service_provider')->default(false)->hint('A service provider is an organization, providing you with managed resources, e.g. AWS or Hetzner'),
                    ]),
                Section::make('Regulation')->schema([
                    Forms\Components\Select::make('importance')->options(DependencyDegree::toMap())->nullable()->hint('Define the importance of this organization. Depending upon the importance, different regulations may apply e.g. for companies subject to NIS2 or KRITIS'),
                ]),
            ])
            ->decorate();
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
            'index' => ListOrganizations::route('/'),
            'create' => CreateOrganization::route('/create'),
            'edit' => EditOrganization::route('/{record}/edit'),
        ];
    }
}
