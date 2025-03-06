<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Organization;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Baremetal;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class Account extends IsKnownConfigurationItem
{
    use HasName;

    protected $table = 'managed_account';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'managed_service_provider_id',
    ];

    public function managedServiceProvider(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'managed_service_provider_id');
    }

    public function baremetals(): BelongsToMany
    {
        return $this->belongsToMany(Baremetal::class, 'managed_baremetal', 'managed_account_id', 'baremetal_id');
    }
}
