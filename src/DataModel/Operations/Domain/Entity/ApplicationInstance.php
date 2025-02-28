<?php

namespace Swark\DataModel\Operations\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Cluster;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Resource;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\System;
use Swark\DataModel\InformationTechnology\Domain\Entity\Zone;
use Swark\DataModel\Kernel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;
use Swark\DataModel\Kernel\Infrastructure\Aspects\IpAddressAssignable;
use Swark\DataModel\Kernel\Infrastructure\Aspects\IpNetworkAssignable;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Release;

class ApplicationInstance extends Model
{
    use HasScompId, AssociatedWithOrganizations, IpNetworkAssignable, IpAddressAssignable;

    protected $table = 'application_instance';

    public $timestamps = true;

    protected $fillable = [
        'scomp_id',
        'release_id',
        'stage_id',
        'executor_id',
        'executor_type',
        'logical_zone_id',
        'system_id',
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
