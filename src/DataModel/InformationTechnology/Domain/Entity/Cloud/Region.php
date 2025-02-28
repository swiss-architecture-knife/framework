<?php

namespace Swark\DataModel\InformationTechnology\Domain\Entity\Cloud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Business\Domain\Entity\Organization;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Region extends Model
{
    use HasScompId;

    protected $table = 'region';
    public $timestamps = false;

    protected $fillable = [
        'scomp_id',
        'name',
        'managed_service_provider_id'
    ];

    public function managedServiceProvider(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'managed_service_provider_id');
    }
}
