<?php

namespace Swark\DataModel\InformationTechnology\Domain\Entity\Network;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Kernel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;

class DnsRecord extends Model
{
    use HasName, AssociatedWithOrganizations;

    protected $table = 'dns_record';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
        'data',
        'dns_zone_id',
        'ip_address_id',
    ];

    protected $attributes = [
        'type' => 'aaaa',
    ];

    public function dnsZone(): BelongsTo
    {
        return $this->belongsTo(DnsZone::class);
    }
}
