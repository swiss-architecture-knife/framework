<?php

namespace Swark\Management\Architecture\Resources\Cloud\OfferResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\Management\Architecture\Resources\Cloud\OfferResource;

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
