<?php

namespace Swark\DataModel\InformationTechnology\Domain\Entity\Cloud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Baremetal;

class ManagedBaremetal extends Model
{
    protected $table = 'managed_baremetal';
    public $timestamps = false;

    protected $fillable = [
        'baremetal_id',
        'managed_offer_id',
        'managed_account_id',
        'availability_zone_id',
    ];

    public function baremetal(): BelongsTo
    {
        return $this->belongsTo(Baremetal::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'managed_offer_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'managed_account_id');
    }

    public function availabilityZone(): BelongsTo
    {
        return $this->belongsTo(AvailabilityZone::class);
    }
}
