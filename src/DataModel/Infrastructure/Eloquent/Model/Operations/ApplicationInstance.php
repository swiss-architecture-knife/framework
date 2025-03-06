<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Operations;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Infrastructure\Aspects\IpAddressAssignable;
use Swark\DataModel\Infrastructure\Aspects\IpNetworkAssignable;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Cluster;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Resource;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\System;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Zone;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Release;

class ApplicationInstance extends IsKnownConfigurationItem
{
    use AssociatedWithOrganizations, IpNetworkAssignable, IpAddressAssignable;

    protected $table = 'application_instance';

    public $timestamps = true;

    protected $fillable = [
        'release_id',
        'stage_id',
        'executor_id',
        'executor_type',
        'logical_zone_id',
        'system_id',
    ];

    public array $scompPathAttributes = [
        'executor_type',
        'executor_id',
        'release_id',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function executor(): MorphTo
    {
        return $this->morphTo();
    }

    public function resources(): MorphMany
    {
        return $this->morphMany(Resource::class, 'provider', 'provider_type', 'provider_id');
    }

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class, 'release_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'logical_zone_id');
    }

    public function clusters(): MorphToMany
    {
        return $this->morphToMany(Cluster::class, 'member', 'cluster_member', 'member_id', 'cluster_id')
            ->withPivot(['is_primary', 'is_active', 'namespace_id']);
    }

    public function deployment(): MorphToMany
    {
        return $this->morphToMany(Deployment::class, 'element', 'deployment_element');
    }
}
