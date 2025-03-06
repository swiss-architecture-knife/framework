<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Cluster;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\ApplicationInstance;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class IpNetwork extends Model
{
    use HasDescription, AssociatedWithOrganizations;

    protected $table = 'ip_network';

    public $timestamps = false;

    protected $fillable = [
        'type',
        'network',
        'network_bin',
        'network_mask',
        'network_mask_bin',
        'gateway_id',
        'description',
        'vlan_id',
    ];

    protected $casts = [
        'network_bin' => \Swark\DataModel\Infrastructure\Casting\IpAddress::class,
        'network_mask_bin' => \Swark\DataModel\Infrastructure\Casting\IpAddress::class,
    ];

    public static function boot()
    {
        parent::boot();

        parent::saving(function ($model) {
            // let it convert to binary
            $model->network_bin = $model->network;
            $model->network_mask_bin = $model->network_mask;
        });
    }

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
