<?php

namespace Swark\DataModel\Infrastructure\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Deployment\Domain\Entity\ApplicationInstance;

class Namespace_ extends Model
{
    protected $table = 'namespace';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'cluster_id',
        'stage_id',
    ];

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class);
    }

    public function applicationInstances(): MorphToMany
    {
        return $this->morphedByMany(ApplicationInstance::class, 'member', 'cluster_member')->withPivot(['is_primary', 'is_active']);
    }
}
