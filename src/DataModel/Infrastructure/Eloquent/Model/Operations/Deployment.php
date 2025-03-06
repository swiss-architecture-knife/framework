<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Operations;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Domain\Model\SoftwareArchitecture\ReleaseTrain;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Subscription;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Cluster;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\ClusterMember;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Resource;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class Deployment extends IsKnownConfigurationItem
{
    protected $table = 'deployment';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'stage_id',
        'release_train_id',
    ];

    public function releaseTrain(): BelongsTo
    {
        return $this->belongsTo(ReleaseTrain::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function resources(): MorphToMany
    {
        return $this->morphedByMany(Resource::class, 'element', 'deployment_element');
    }

    public function applicationInstances(): MorphToMany
    {
        return $this->morphedByMany(ApplicationInstance::class, 'element', 'deployment_element');
    }

    public function subscriptions(): MorphToMany
    {
        return $this->morphedByMany(Subscription::class, 'element', 'deployment_element');
    }

    public function cluster(): HasOneThrough {
        return $this->hasOneThrough(Cluster::class, ClusterMember::class, 'member_id', 'id', 'c','cluster_id')
            ->where('cluster_member.member_type','=','deployment')
            ->where('cluster_member.id', '1');
    }
    public function clusters(): MorphToMany
    {
        return $this->morphedByMany(Cluster::class, 'member', 'cluster_member', 'member_id', 'cluster_id')
            ->withPivot(['is_primary', 'is_active', 'namespace_id']);
    }
}
