<?php

namespace Swark\DataModel\Infrastructure\Aspects;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\IpAddress;

trait IpAddressAssignable
{
    public function ipAddresses(): MorphToMany
    {
        return $this->morphToMany(IpAddress::class, 'assignable', 'ip_address_assigned')->withPivot(['description']);
    }
}
