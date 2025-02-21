<?php

namespace Swark\DataModel\Network\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Deployment\Domain\Entity\ApplicationInstance;
use Swark\DataModel\Infrastructure\Domain\Entity\Cluster;

class IpAddress extends Model
{
    protected $table = 'ip_address';

    public $timestamps = false;

    protected $fillable = [
        'address',
        'ip_network_id',
        'description'
    ];

    protected $casts = [
        'address' => \Swark\DataModel\Kernel\Infrastructure\Casting\IpAddress::class
    ];

    public function nics(): MorphToMany
    {
        return $this->morphToMany(Nic::class, 'assignable', 'ip_address_assigned');
    }

    public function clusters(): MorphToMany
    {
        return $this->morphToMany(Cluster::class, 'assignable', 'ip_address_assigned');
    }

    public function applicationInstances(): MorphToMany
    {
        return $this->morphToMany(ApplicationInstance::class, 'assignable', 'ip_address_assigned');
    }

    /**
     * Make IP address searchable. As we store it as VARBINARY(16), search parameters have to be converted
     * @param $query
     * @param string $ipAddress
     * @return mixed
     */
    public function scopeWithIp($query, string $ipAddress)
    {
        return $query->whereRaw('address = INET6_ATON(?)', [$ipAddress]);
    }

    public function ipNetwork(): BelongsTo
    {
        return $this->belongsTo(IpNetwork::class);
    }
}
