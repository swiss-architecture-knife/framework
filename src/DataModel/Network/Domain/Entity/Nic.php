<?php

namespace Swark\DataModel\Network\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Swark\DataModel\Business\Domain\Entity\Organization;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\IpAddressAssignable;

class Nic extends Model
{
    use HasName, IpAddressAssignable;

    protected $table = 'nic';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'mac_address',
        'vendor_id',
        'vlan_id',
        'equipable_type',
        'equipable_id',
    ];

    public function vlan(): BelongsTo
    {
        return $this->belongsTo(Vlan::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'vendor_id');
    }

    public function equipable(): MorphTo
    {
        return $this->morphTo();
    }
}
