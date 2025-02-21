<?php

namespace Swark\Management\Architecture\Resources\Cloud;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Cloud\Domain\Entity\Offer;
use Swark\Management\Resources\Shared;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    protected static ?string $navigationGroup = Shared::CLOUD;

    protected static ?int $navigationSort = 3;

    protected static ?string $label = 'Managed offer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shared::defaultGeneralSection(),
                Section::make('Offer provided by service provider')->schema([
                    Shared::selectServiceProvider(),
                ]),
                Section::make('Managed software')->description('Offer provides a managed software product')->schema([
                    Shared::searchableSoftware(multiple: false)
                        ->required(false)
                        ->hint('You can select a software from your service catalog if this is a managed software. If you want to manage baremetals, leave that open.'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('managedServiceProvider.name'),
                Tables\Columns\TextColumn::make('name'),
            ])->defaultSort('id')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Swark\Management\Architecture\Resources\Cloud\OfferResource\Pages\ListOffers::route('/'),
            'create' => \Swark\Management\Architecture\Resources\Cloud\OfferResource\Pages\CreateOffer::route('/create'),
            'edit' => \Swark\Management\Architecture\Resources\Cloud\OfferResource\Pages\EditOffer::route('/{record}/edit'),
        ];
    }
}
