<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Cloud\Domain\Entity\Account;
use Swark\DataModel\Cloud\Domain\Entity\AvailabilityZone;
use Swark\DataModel\Cloud\Domain\Entity\ManagedBaremetal;
use Swark\DataModel\Cloud\Domain\Entity\Region;
use Swark\DataModel\Infrastructure\Domain\Entity\Baremetal;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

class BaremetalsSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle, SkipsEmptyRows
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const DESCRIPTION_COLUMN = 'description';
    const MANAGED_OFFER_SCOMP_ID = 'managed-offer-scomp-id';
    const MANAGED_ACCOUNT_SCOMP_ID = 'managed-account-scomp-id';
    const REGION_SCOMP_ID = 'region-scomp-id';
    const AZ_SCOMP_ID = 'az-scomp-id';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'description', 'managed-offer-scomp-id', 'managed-account-scomp-id', 'region-scomp-id', 'az-scomp-id'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Description', static::DESCRIPTION_COLUMN))
            ->add(Column::of('Managed service')->span(4))
            ->next()
            ->add(Column::empty(3))
            ->add(Column::of('Offer', static::MANAGED_OFFER_SCOMP_ID))
            ->add(Column::of('Account', static::MANAGED_ACCOUNT_SCOMP_ID))
            ->add(Column::of('Region', static::REGION_SCOMP_ID))
            ->add(Column::of('Availability zone', static::AZ_SCOMP_ID))
            ->next()
            ->add(Column::empty(3))
            ->add(Column::of('${managed_offer.scomp_id}?'))
            ->add(Column::of('${managed_account.scomp_id}?'))
            ->add(Column::of('region.scomp_id'))
            ->add(Column::of('availability_zone.scomp_id'));
    }


    public function title(): string
    {
        return "Baremetals";
    }

    protected function importRow(RowContext $row)
    {
        $baremetal = Baremetal::updateOrCreate([
            'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
            'name' => $row[static::NAME_COLUMN],
            'description' => $row[static::DESCRIPTION_COLUMN],
        ]);

        $this->compositeKeyContainer->set('baremetal', $baremetal->scomp_id, $baremetal->id);
        $managedAccountScompId = $row[static::MANAGED_ACCOUNT_SCOMP_ID];
        $managedOfferScompId = $row[static::MANAGED_OFFER_SCOMP_ID];

        if (empty($managedOfferScompId) || empty($managedAccountScompId)) {
            return;
        }

        $account = Account::where('scomp_id', $managedAccountScompId)->firstOrFail();
        $region = Region::updateOrCreate([
            'name' => $row[static::REGION_SCOMP_ID],
            'managed_service_provider_id' => $account->managed_service_provider_id
        ]);
        $this->compositeKeyContainer->set('region', $account->managed_service_provider_id . ":" . $region->scomp_id, $region->id);

        $az = AvailabilityZone::updateOrCreate([
            'name' => $row[static::AZ_SCOMP_ID],
            'region_id' => $region->id
        ]);
        $this->compositeKeyContainer->set('availability_zone', $account->managed_service_provider_id . ":" . $region->scomp_id . ":" . $az->scomp_id, $az->id);

        ManagedBaremetal::updateOrCreate([
            'baremetal_id' => $baremetal->id,
            'managed_offer_id' => $this->compositeKeyContainer->get('managed_offer', $managedOfferScompId),
            'managed_account_id' => $this->compositeKeyContainer->get('managed_account', $managedAccountScompId),
            'availability_zone_id' => $az->id,
        ]);
    }
}

