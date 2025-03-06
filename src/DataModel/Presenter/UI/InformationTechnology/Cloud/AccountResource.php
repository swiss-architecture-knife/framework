<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Cloud;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Account;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\InformationTechnology\Cloud\AccountResource\Pages\CreateAccount;
use Swark\DataModel\Presenter\UI\InformationTechnology\Cloud\AccountResource\Pages\EditAccount;
use Swark\DataModel\Presenter\UI\InformationTechnology\Cloud\AccountResource\Pages\ListAccounts;
use Swark\DataModel\Presenter\UI\Shared;

class AccountResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Account::class;

    protected static ?string $navigationGroup = Shared::CLOUD;

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->sections([
                Section::make('Account for service provider')->schema([
                    Shared::selectServiceProvider(),
                ])
            ])->decorate();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('managedServiceProvider.name')->label('Service provider'),
                Tables\Columns\TextColumn::make('name')->label('Account ID or name'),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccounts::route('/'),
            'create' => CreateAccount::route('/create'),
            'edit' => EditAccount::route('/{record}/edit'),
        ];
    }
}
