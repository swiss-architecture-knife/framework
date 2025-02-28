<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\SubscriptionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Cloud\SubscriptionResource;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
