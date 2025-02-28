<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\InformationTechnology\Domain\Entity\Cloud\Offer;
use Swark\DataModel\Kernel\Infrastructure\UI\Shared;

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
            'index' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\OfferResource\Pages\ListOffers::route('/'),
            'create' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\OfferResource\Pages\CreateOffer::route('/create'),
            'edit' => \Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\OfferResource\Pages\EditOffer::route('/{record}/edit'),
        ];
    }
}
