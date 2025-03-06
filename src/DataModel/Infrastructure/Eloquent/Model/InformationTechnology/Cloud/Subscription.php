<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Cluster;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Resource;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Zone;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\Deployment;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Release;

class Subscription extends IsKnownConfigurationItem
{
    use HasName, AssociatedWithOrganizations;

    protected $table = 'managed_subscription';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'managed_offer_id',
        'managed_account_id',
        'logical_zone_id',
        'availability_zone_id',
        'release_id'
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'managed_account_id');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'managed_offer_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'logical_zone_id');
    }

    public function availabilityZone(): BelongsTo
    {
        return $this->belongsTo(AvailabilityZone::class);
    }

    public function resources(): MorphMany
    {
        return $this->morphMany(Resource::class, 'provider', 'provider_type', 'provider_id');
    }

    public function clusters(): MorphToMany
    {
        return $this->morphToMany(Cluster::class, 'member', 'cluster_member', 'member_id', 'cluster_id');
    }

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class, 'release_id');
    }

    public function deployments(): MorphToMany
    {
        return $this->morphToMany(Deployment::class, 'element', 'deployment_element');
    }
}
