<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Cloud;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Organization;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Account;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;

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
        $vendor = Organization::upsert([
            'name' => $row->nonEmpty(static::VENDOR_COLUMN),
        ], [
            'is_managed_service_provider' => true
        ]);

        $this->compositeKeyContainer->set('organization', $vendor->configurationItem->scomp_id, $vendor->id);

        $managedAccount = Account::upsert(
            $row->nonEmpty(Column::SCOMP_ID_COLUMN),
            [
                'managed_service_provider_id' => $vendor->id,
                'name' => $row[static::NAME_COLUMN],
            ]);

        $this->compositeKeyContainer->set('managed_account', $managedAccount->configurationItem->scomp_id, $managedAccount->id);
    }
}

