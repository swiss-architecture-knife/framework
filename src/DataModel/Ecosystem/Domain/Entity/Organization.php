<?php

namespace Swark\DataModel\Ecosystem\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Cloud\Entity\Account;
use Swark\DataModel\Cloud\Entity\Subscription;
use Swark\DataModel\Deployment\Domain\Entity\ApplicationInstance;
use Swark\DataModel\Enterprise\Domain\Entity\Zone;
use Swark\DataModel\Infrastructure\Domain\Entity\Baremetal;
use Swark\DataModel\Infrastructure\Domain\Entity\Cluster;
use Swark\DataModel\Infrastructure\Domain\Entity\Host;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;
use Swark\DataModel\Network\Domain\Entity\DnsZone;
use Swark\DataModel\Network\Domain\Entity\IpNetwork;
use Swark\DataModel\Network\Domain\Entity\Vlan;

class Organization extends Model
{
    use HasScompId, HasName;

    protected $table = 'organization';

    protected $fillable = [
        'name',
        'scomp_id',
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
