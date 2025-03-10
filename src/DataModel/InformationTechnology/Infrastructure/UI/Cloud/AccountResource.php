<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\InformationTechnology\Domain\Entity\Cloud\Account;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationGroup = Shared::CLOUD;

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(),
                Section::make('Account for service provider')->schema([
                    Shared::selectServiceProvider(),
                ])
            ]);
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
            'index' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\AccountResource\Pages\ListAccounts::route('/'),
            'create' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\AccountResource\Pages\CreateAccount::route('/create'),
            'edit' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\AccountResource\Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
