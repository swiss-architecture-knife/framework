<?php

namespace Swark\DataModel\Cloud\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Ecosystem\Domain\Entity\Organization;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;
use Swark\DataModel\Software\Domain\Entity\Software;

class Offer extends Model
{
    use HasScompId, HasName;

    protected $table = 'managed_offer';
    public $timestamps = true;

    protected $fillable = [
        'scomp_id',
        'name',
        'managed_service_provider_id',
        'software_id'
    ];

    public function software(): BelongsTo
    {
        return $this->belongsTo(Software::class);
    }

    public function managedServiceProvider(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'managed_service_provider_id');
    }
}
