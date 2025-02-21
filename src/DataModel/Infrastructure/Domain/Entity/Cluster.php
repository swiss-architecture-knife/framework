<?php

namespace Swark\DataModel\Infrastructure\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Cloud\Domain\Entity\Subscription;
use Swark\DataModel\Deployment\Domain\Entity\ApplicationInstance;
use Swark\DataModel\Deployment\Domain\Entity\Deployment;
use Swark\DataModel\Deployment\Domain\Entity\Stage;
use Swark\DataModel\Kernel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;
use Swark\DataModel\Kernel\Infrastructure\Aspects\IpAddressAssignable;
use Swark\DataModel\Kernel\Infrastructure\Aspects\IpNetworkAssignable;
use Swark\DataModel\Software\Domain\Entity\Release;

class Cluster extends Model
{
    use HasScompId, HasName, AssociatedWithOrganizations, IpAddressAssignable, IpNetworkAssignable;

    protected $table = 'cluster';

    public $timestamps = true;

    protected $fillable = [
        'scomp_id',
        'name',
        'type',
        'target_software_id',
        'target_release_id',
        'stage_id',
        'mode',
        'virtual_name',
    ];

    public function namespaces(): HasMany
    {
        return $this->hasMany(Namespace_::class);
    }

    public function targetRelease(): BelongsTo
    {
        return $this->belongsTo(Release::class, 'target_release_id');
    }

    public function resources(): MorphMany
    {
        return $this->morphMany(Resource::class, 'provider', 'provider_type', 'provider_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function subscriptions(): MorphToMany
    {
        return $this->morphedByMany(Subscription::class, 'member', 'cluster_member');
    }

    public function runtimes(): MorphToMany
    {
        return $this->morphedByMany(Runtime::class, 'member', 'cluster_member');
    }

    const NAMESPACE_NAME_JOINED = 'namespace_name';

    private function morphClusterMember(string $related): MorphToMany
    {
        return $this->morphedByMany($related, 'member', 'cluster_member')
            // we have to join the namespace; we can not retrieve that easily with Filament
            ->withPivot(['is_primary', 'is_active', 'namespace_id', 'namespace.name AS ' . static::NAMESPACE_NAME_JOINED])
            ->leftJoin('namespace', 'namespace.id', '=', 'cluster_member.namespace_id');
    }

    public function applicationInstances(): MorphToMany
    {
        return $this->morphClusterMember(ApplicationInstance::class);
    }

    public function deployments(): MorphToMany
    {
        return $this->morphClusterMember(Deployment::class);
    }
}
