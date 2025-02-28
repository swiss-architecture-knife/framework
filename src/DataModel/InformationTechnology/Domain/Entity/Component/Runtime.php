<?php

namespace Swark\DataModel\InformationTechnology\Domain\Entity\Component;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Deployment\Domain\Entity\ApplicationInstance;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;
use Swark\DataModel\Kernel\Infrastructure\Aspects\IsConfigurationItem;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Release;


class Runtime extends Model
{
    use HasScompId, HasName, IsConfigurationItem;

    protected $table = 'runtime';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'scomp_id',
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
