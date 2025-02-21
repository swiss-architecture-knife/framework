<?php

namespace Swark\Api\Server\Domain\Baremetal;

use Swark\Api\Server\Internal\BaseRequest;
use Swark\Api\Server\Internal\NamingContext;
use Swark\DataModel\Cloud\Domain\Entity\Account;
use Swark\DataModel\Cloud\Domain\Entity\AvailabilityZone;
use Swark\DataModel\Cloud\Domain\Entity\Region;

class BaremetalRequest extends BaseRequest
{

    public ?Region $region = null;
    public ?AvailabilityZone $availabilityZone = null;
    public ?Account $account = null;

    public function rules()
    {
        return $rules = [
            'name' => 'required',
            'placement' => [
                'nullable',
                function ($attribute, $placement, $fail) {
                    try {
                        $this->region = NamingContext::ofNamedReference(Region::class, $placement['region'])->resolve();
                        $this->availabilityZone = NamingContext::ofNamedReference(AvailabilityZone::class, $placement['availability_zone'])->resolve();
                        $this->account = NamingContext::ofNamedReference(Account::class, $placement['account'])->resolve();

                        if ($this->account?->managed_service_provider_id != $this->region->managed_service_provider_id) {
                            return $fail("Availability zone does not belong to account of given managed service provider");
                        }

                    } catch (\Exception $e) {
                        return $fail("Unable to resolve one of region:{$this->region}, az:{$this->availabilityZone}, account:{$this->account}: {$e->getMessage()}");
                    }

                    return true;
                }],
        ];
    }
}
