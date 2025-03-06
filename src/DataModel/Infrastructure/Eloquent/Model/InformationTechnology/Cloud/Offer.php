<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Organization;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Software;

class Offer extends IsKnownConfigurationItem
{
    use HasName;

    protected $table = 'managed_offer';
    public $timestamps = true;

    protected $fillable = [
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
