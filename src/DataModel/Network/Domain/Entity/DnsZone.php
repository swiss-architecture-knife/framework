<?php

namespace Swark\DataModel\Network\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Kernel\Infrastructure\Aspects\AssociatedWithOrganizations;

class DnsZone extends Model
{
    use AssociatedWithOrganizations;

    protected $table = 'dns_zone';

    public $timestamps = false;

    protected $fillable = [
        'zone',
        'parent_dns_zone_id',
    ];

    public function records(): HasMany
    {
        return $this->hasMany(DnsRecord::class);
    }

    public function parentDnsZone(): BelongsTo
    {
        return $this->belongsTo(DnsZone::class, 'parent_dns_zone_id');
    }
}
