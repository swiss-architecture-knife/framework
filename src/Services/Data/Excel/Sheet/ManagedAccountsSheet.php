<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Business\Domain\Entity\Organization;
use Swark\DataModel\Cloud\Domain\Entity\Account;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

class ManagedAccountsSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle, SkipsEmptyRows
{
    use Exportable;

    const VENDOR_COLUMN = 'vendor';
    const NAME_COLUMN = 'name';
    const NOTES_COLUMN = 'notes';

    public function generator(): \Generator
    {
        yield ['vendor', 'name', 'scomp-id', 'notes'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Vendor', static::VENDOR_COLUMN))
            ->add(Column::of('Account')->span(2))
            ->add(Column::of('Notes', static::NOTES_COLUMN))
            ->next()
            ->add(Column::empty())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::empty());
    }


    public function title(): string
    {
        return "Managed accounts";
    }

    protected function importRow(RowContext $row)
    {
        $organizationDataUnique = ['name' => $row->nonEmpty(static::VENDOR_COLUMN), 'is_managed_service_provider' => true];
        $organizationDataAltered = [];

        try {
            $scompId = Organization::toScompId($row[static::VENDOR_COLUMN]);
            $id = $this->compositeKeyContainer->get('organization', $scompId);

            $organizationDataUnique = ['scomp_id' => $scompId];
            $organizationDataAltered = ['is_managed_service_provider' => true];
        } catch (\Exception $e) {
            // ignore
        }

        $vendor = Organization::updateOrCreate($organizationDataUnique, $organizationDataAltered);
        $this->compositeKeyContainer->set('organization', $vendor->scomp_id, $vendor->id);

        $managedAccount = Account::updateOrCreate(
            [
                'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
            ], [
            'managed_service_provider_id' => $vendor->id,
            'name' => $row[static::NAME_COLUMN],
        ]);

        $this->compositeKeyContainer->set('managed_account', $managedAccount->scomp_id, $managedAccount->id);
    }
}

