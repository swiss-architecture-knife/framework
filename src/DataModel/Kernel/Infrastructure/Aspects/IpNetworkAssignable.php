<?php

namespace Swark\DataModel\Kernel\Infrastructure\Aspects;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Network\Domain\Entity\IpNetwork;

trait IpNetworkAssignable
{
    public function ipNetworks(): MorphToMany
    {
        return $this->morphToMany(IpNetwork::class, 'assignable', 'ip_network_assigned')->withPivot(['description']);
    }
}
