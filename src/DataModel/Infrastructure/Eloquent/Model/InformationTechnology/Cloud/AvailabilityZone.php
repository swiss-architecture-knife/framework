<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class AvailabilityZone extends IsKnownConfigurationItem
{
    use HasName;

    protected $table = 'availability_zone';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'region_id'
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
