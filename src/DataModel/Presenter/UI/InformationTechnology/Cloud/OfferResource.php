<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Cloud;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Offer;
use Swark\DataModel\Presenter\UI\Concerns\HasDataModelDecorator;
use Swark\DataModel\Presenter\UI\Shared;

class OfferResource extends Resource
{
    use HasDataModelDecorator;

    protected static ?string $model = Offer::class;

    protected static ?string $navigationGroup = Shared::CLOUD;

    protected static ?int $navigationSort = 3;

    protected static ?string $label = 'Managed offer';

    public static function form(Form $form): Form
    {
        return static::decorator($form)
            ->sections([
                Section::make('Offer provided by service provider')->schema([
                    Shared::selectServiceProvider(),
                ]),
                Section::make('Managed software')->description('Offer provides a managed software product')->schema([
                    Shared::searchableSoftware(multiple: false)
                        ->required(false)
                        ->hint('You can select a software from your service catalog if this is a managed software. If you want to manage baremetals, leave that open.'),
                ])
            ])
            ->decorate();
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
            'index' => OfferResource\Pages\ListOffers::route('/'),
            'create' => OfferResource\Pages\CreateOffer::route('/create'),
            'edit' => OfferResource\Pages\EditOffer::route('/{record}/edit'),
        ];
    }
}
