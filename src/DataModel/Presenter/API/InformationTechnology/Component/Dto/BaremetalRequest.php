<?php

namespace Swark\DataModel\Presenter\API\InformationTechnology\Component\Dto;

use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Account;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\AvailabilityZone;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Region;
use Swark\DataModel\Presenter\API\BaseRequest;
use Swark\DataModel\Presenter\API\NamingContext;

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
