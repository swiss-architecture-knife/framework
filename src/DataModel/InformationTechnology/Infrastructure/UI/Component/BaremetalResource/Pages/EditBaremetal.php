<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\BaremetalResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\BaremetalResource;

class EditBaremetal extends EditRecord
{
    use NestedPage;

    protected static string $resource = BaremetalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // when showing a baremetal, we have to set the checkbox in the frontend if the baremetal is already managed or not
        $data[BaremetalResource::IS_MANAGED_VIRTUAL_FIELD] = $this->getRecord()->managed !== null;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // if checkbox has not been checked, remove the hasOne/one-to-one "managed" relationship of the baremetal model
        if (empty($data[BaremetalResource::IS_MANAGED_VIRTUAL_FIELD]) || !$data[BaremetalResource::IS_MANAGED_VIRTUAL_FIELD]) {
            $record->managed()->delete();
        }

        return $record;
    }
}
