<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Business;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Account;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Subscription;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Baremetal;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Cluster;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Host;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\DnsZone;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\IpNetwork;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\Vlan;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Zone;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\ApplicationInstance;

class Organization extends IsKnownConfigurationItem
{
    use HasName;

    protected $table = 'organization';

    protected $fillable = [
        'name',
        'is_internal',
        'is_vendor',
        'is_customer',
        'is_managed_service_provider',
        // NIS2 importance
        'importance',
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'managed_service_provider_id');
    }

    public function __call($method, $parameters)
    {
        // forward associatable items
        $associatbale = [
            'zones' => Zone::class,
            'vlans' => Vlan::class,
            'dnsZones' => DnsZone::class,
            'ipNetworks' => IpNetwork::class,
            'baremetals' => Baremetal::class,
            'hosts' => Host::class,
            'clusters' => Cluster::class,
            'applicationInstances' => ApplicationInstance::class,
            'managedSubscriptions' => Subscription::class,
        ];

        if (isset($associatbale[$method])) {
            return $this->morphedByMany($associatbale[$method], 'associatable', 'associated_with_organization')->withPivot(['role']);
        }

        return parent::__call($method, $parameters);
    }
}
