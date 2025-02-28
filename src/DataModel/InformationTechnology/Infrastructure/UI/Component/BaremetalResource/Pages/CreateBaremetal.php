<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\BaremetalResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\BaremetalResource;

class CreateBaremetal extends CreateRecord
{
    use NestedPage;

    protected static string $resource = BaremetalResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // for new baremetals, we assume that they are not managed
        $data[BaremetalResource::IS_MANAGED_VIRTUAL_FIELD] = false;

        return $data;
    }
}
