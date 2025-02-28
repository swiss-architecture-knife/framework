<?php

namespace Swark\Services\Data\Excel\Sheet;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\InformationTechnology\Domain\Entity\Cloud\Subscription;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Resource;
use Swark\DataModel\ModelTypes;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;
use Swark\Services\Data\ResolvedScompType;

class ManagedSubscriptionsSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle, SkipsEmptyRows
{
    use Exportable;

    const ACCOUNT_SCOMP_ID_COLUMN = 'account-scomp-id';
    const OFFER_SCOMP_ID_COLUMN = 'offer-scomp-id';
    const NAME_COLUMN = 'name';
    const RESOURCES_SCOMP_ID_COLUMN = 'resources-scomp-id';

    public function generator(): \Generator
    {
        yield ['account-scomp-id', 'offer-scomp-id', 'name', 'resource-1-scomp-id,resource-2-scomp-id'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Account', static::ACCOUNT_SCOMP_ID_COLUMN))
            ->add(Column::of('Offer', static::OFFER_SCOMP_ID_COLUMN))
            ->add(Column::of('Subscription'))
            ->add(Column::of('Resources', static::RESOURCES_SCOMP_ID_COLUMN))
            ->next()
            ->add(Column::of('${managed_account.scomp_id}'))
            ->add(Column::of('${managed_offer.scomp_id}'))
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::of('${resource_type.scomp_id}:scomp_id'));
    }


    public function title(): string
    {
        return "Managed subscriptions";
    }

    protected function importRow(RowContext $row)
    {
        $accountId = $this->compositeKeyContainer->get('managed_account', $row->nonEmpty(static::ACCOUNT_SCOMP_ID_COLUMN));
        $offerId = $this->compositeKeyContainer->get('managed_offer', $row->nonEmpty(static::OFFER_SCOMP_ID_COLUMN));

        $managedSubscription = Subscription::updateOrCreate([
            'managed_account_id' => $accountId,
            'managed_offer_id' => $offerId,
            'name' => $row[static::NAME_COLUMN],
        ]);

        $this->compositeKeyContainer->set('managed_subscription', $managedSubscription->scomp_id, $managedSubscription->id);

        $this
            ->compositeKeyContainer
            ->findScompIds($row[static::RESOURCES_SCOMP_ID_COLUMN], [ModelTypes::ResourceType->value], ['scomp_id'])
            ->forEach(function (ResolvedScompType $resolvedScompType, array $args) use ($managedSubscription) {
                $resource = Resource::updateOrCreate([
                    'name' => $args['scomp_id'],
                    'resource_type_id' => $resolvedScompType->internalId,
                    'provider_id' => $managedSubscription->id,
                    'provider_type' => ModelTypes::Subscription,
                ]);

                $this->compositeKeyContainer->set('resource', $managedSubscription->scomp_id . ':' . $resource->name, $resource->id);

                // register shortcut
                if (!isset($this->map['resource'][$resource->name])) {
                    $this->compositeKeyContainer->set('resource', $resource->name, $resource->id);
                }
            });
    }
}

