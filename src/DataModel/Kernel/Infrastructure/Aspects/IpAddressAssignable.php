<?php

namespace Swark\DataModel\Kernel\Infrastructure\Aspects;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\IpAddress;

trait IpAddressAssignable
{
    public function ipAddresses(): MorphToMany
    {
        return $this->morphToMany(IpAddress::class, 'assignable', 'ip_address_assigned')->withPivot(['description']);
    }
}
