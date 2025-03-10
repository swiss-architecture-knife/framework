<?php

namespace Swark\DataModel\InformationTechnology\Domain\Entity\Cloud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Business\Domain\Entity\Organization;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Software;

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
