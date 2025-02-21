<?php

namespace Swark\DataModel\Network\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Deployment\Domain\Entity\ApplicationInstance;
use Swark\DataModel\Infrastructure\Domain\Entity\Cluster;
use Swark\DataModel\Kernel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class IpNetwork extends Model
{
    use HasScompId, HasDescription, AssociatedWithOrganizations;

    protected $table = 'ip_network';

    public $timestamps = false;

    protected $fillable = [
        'scomp_id',
        'type',
        'network',
        'network_mask',
        'gateway_id',
        'description',
        'vlan_id',
    ];

    protected $casts = [
        'network' => \Swark\DataModel\Kernel\Infrastructure\Casting\IpAddress::class,
        'network_mask' => \Swark\DataModel\Kernel\Infrastructure\Casting\IpAddress::class,
        'gateway' => \Swark\DataModel\Kernel\Infrastructure\Casting\IpAddress::class,
    ];

    public function vlan(): BelongsTo
    {
        return $this->belongsTo(Vlan::class);
    }

    public function clusters(): MorphToMany
    {
        return $this->morphedByMany(Cluster::class, 'assignable', 'ip_network_assigned');
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(IpAddress::class, 'gateway_id');
    }

    public function applicationInstances(): MorphToMany
    {
        return $this->morphedByMany(ApplicationInstance::class, 'assignable', 'ip_network_assigned');
    }

    public function ipAddresses(): HasMany
    {
        return $this->hasMany(IpAddress::class);
    }
}
