<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\OfferResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\OfferResource;

class EditOffer extends EditRecord
{
    protected static string $resource = OfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
