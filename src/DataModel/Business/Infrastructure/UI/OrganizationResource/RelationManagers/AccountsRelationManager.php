<?php

namespace Swark\DataModel\Business\Infrastructure\UI\OrganizationResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

class AccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'accounts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::requiredName(),
                Shared::scompId(),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return (bool)$ownerRecord->is_service_provider;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
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
