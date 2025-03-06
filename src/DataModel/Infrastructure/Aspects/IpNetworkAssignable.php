<?php

namespace Swark\DataModel\Infrastructure\Aspects;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\IpNetwork;

trait IpNetworkAssignable
{
    public function ipNetworks(): MorphToMany
    {
        return $this->morphToMany(IpNetwork::class, 'assignable', 'ip_network_assigned')->withPivot(['description']);
    }
}
