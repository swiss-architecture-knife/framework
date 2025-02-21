<?php

namespace Swark\Api\Client\Domain\Baremetal;

use Swark\Api\Client\Domain\JsonDataResponse;

readonly class ManagedBaremetalResponse
{
    public function __construct(
        public AccountResponse          $account,
        public ?OfferResponse           $offer,
        public RegionResponse           $region,
        public AvailabilityZoneResponse $availabilityZone,
    )
    {
    }

    public static function of(JsonDataResponse|array $item): ManagedBaremetalResponse
    {
        return new ManagedBaremetalResponse(
            account: AccountResponse::of($item['account']),
            offer: isset($item['offer']) ? OfferResponse::of($item['offer']) : null,
            region: RegionResponse::of($item['region']),
            availabilityZone: AvailabilityZoneResponse::of($item['availability_zone'])
        );
    }
}
