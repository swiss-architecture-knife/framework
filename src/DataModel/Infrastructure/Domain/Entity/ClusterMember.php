<?php

namespace Swark\DataModel\Infrastructure\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ClusterMember extends Model
{
    protected $table = 'cluster_member';

    public $timestamps = true;

    protected $fillable = [
        'cluster_id',
        'member_type',
        'member_id',
        'is_primary',
        'is_active',
        'namespace_id',
    ];

    public function member(): MorphTo
    {
        return $this->morphTo();
    }

    public function namespace(): BelongsTo
    {
        return $this->belongsTo(Namespace_::class, 'namespace_id');
    }

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class);
    }
}
