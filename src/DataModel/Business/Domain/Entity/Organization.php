<?php

namespace Swark\DataModel\Business\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Deployment\Domain\Entity\ApplicationInstance;
use Swark\DataModel\InformationTechnology\Domain\Entity\Cloud\Account;
use Swark\DataModel\InformationTechnology\Domain\Entity\Cloud\Subscription;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Baremetal;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Cluster;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Host;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\DnsZone;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\IpNetwork;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\Vlan;
use Swark\DataModel\InformationTechnology\Domain\Entity\Zone;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

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
