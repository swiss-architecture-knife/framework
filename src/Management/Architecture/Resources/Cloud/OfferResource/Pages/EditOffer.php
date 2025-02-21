<?php

namespace Swark\Management\Architecture\Resources\Cloud\OfferResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Swark\Management\Architecture\Resources\Cloud\OfferResource;

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
