<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Organization;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class Region extends IsKnownConfigurationItem
{
    use HasName;

    protected $table = 'region';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'managed_service_provider_id'
    ];

    public function managedServiceProvider(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'managed_service_provider_id');
    }
}
