<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\ApplicationInstance;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Release;


class Runtime extends IsKnownConfigurationItem
{
    use HasName;

    protected $table = 'runtime';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'host_id',
        'release_id',
    ];

    public static function boot()
    {
        parent::boot();
    }

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }

    public function clusters(): MorphToMany
    {
        return $this->morphToMany(Cluster::class, 'member', 'cluster_member', 'member_id', 'cluster_id');
    }

    public function executables(): MorphToMany
    {
        return $this->morphToMany(ApplicationInstance::class, 'executors');
    }
}
