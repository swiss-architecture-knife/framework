<?php

namespace Swark\Management\Architecture\Resources\Ecosystem\OrganizationResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AssociatedWithOrganizationsRelationManager extends RelationManager
{
    protected static string $relationship = 'associatedWithOrganizations';

    protected static ?string $title = "Associated with organizations";

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('pivot.role')->label('Role'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make('associatedWith')
                    ->form(function(Form $form, Tables\Actions\AttachAction $action) {
                        return [
                            $action->getRecordSelect(),
                            Select::make('role')->label('Role')->options(['owner' => 'Owner', 'customer' => 'Customer', 'manager' => 'Manager'])
                        ];
                    })
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
