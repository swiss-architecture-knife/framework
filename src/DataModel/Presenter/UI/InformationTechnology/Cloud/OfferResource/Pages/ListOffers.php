<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Cloud\OfferResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\Presenter\UI\InformationTechnology\Cloud\OfferResource;

class ListOffers extends ListRecords
{
    protected static string $resource = OfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
