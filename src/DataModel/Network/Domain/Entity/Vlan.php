<?php

namespace Swark\DataModel\Network\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Kernel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Vlan extends Model
{
    use HasScompId, AssociatedWithOrganizations;

    protected $table = 'vlan';

    public $timestamps = false;

    protected $fillable = [
        'scomp_id',
        'number',
    ];

    public function ipNetworks(): HasMany
    {
        return $this->hasMany(IpNetwork::class);
    }
}
