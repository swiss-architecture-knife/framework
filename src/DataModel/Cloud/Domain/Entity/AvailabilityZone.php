<?php

namespace Swark\DataModel\Cloud\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class AvailabilityZone extends Model
{
    use HasScompId, HasName;

    protected $table = 'availability_zone';
    public $timestamps = false;

    protected $fillable = [
        'scomp_id',
        'name',
        'region_id'
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
