<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Cloud\Domain\Entity\Account;
use Swark\DataModel\Cloud\Domain\Entity\Offer;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

class ManagedOffersSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle, SkipsEmptyRows
{
    use Exportable;

    const VENDOR_COLUMN = 'vendor';
    const NAME_COLUMN = 'name';
    const SOFTWARE_SCOMP_ID_COLUMN = 'software-scomp-id';

    public function generator(): \Generator
    {
        yield ['vendor', 'name', 'scomp-id', 'software-scomp-id'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Vendor', static::VENDOR_COLUMN))
            ->add(Column::of('Offer')->span(3))
            ->next()
            ->add(Column::empty())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Software::scomp_id', static::SOFTWARE_SCOMP_ID_COLUMN))
            ->next()
            ->add(Column::empty(3))
            ->add(Column::of('${software.scomp_id}?'));
    }


    public function title(): string
    {
        return "Managed offers";
    }

    protected function importRow(RowContext $row)
    {
        $vendorScompId = Account::toScompId($row->nonEmpty(static::VENDOR_COLUMN));
        $vendorId = $this->compositeKeyContainer->get('organization', $vendorScompId);

        $targetSoftwareId = null;

        if (!empty($softwareScompId = $row[static::SOFTWARE_SCOMP_ID_COLUMN])) {
            $targetSoftwareId = $this->compositeKeyContainer->get('software', $softwareScompId);
        }

        $managedOffer = Offer::updateOrCreate([
            'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN)
        ], [
            'name' => $row[static::NAME_COLUMN],
            'managed_service_provider_id' => $vendorId,
            'software_id' => $targetSoftwareId,
        ]);

        $this->compositeKeyContainer->set('managed_offer', $managedOffer->scomp_id, $managedOffer->id);
    }
}

