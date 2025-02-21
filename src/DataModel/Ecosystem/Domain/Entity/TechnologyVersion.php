<?php

namespace Swark\DataModel\Ecosystem\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TechnologyVersion extends Model
{
    protected $table = 'technology_version';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'scomp_id',
        'technology_id',
        'is_latest'
    ];

    public function technology(): BelongsTo
    {
        return $this->belongsTo(Technology::class);
    }

    public function resourceTypes(): HasMany
    {
        return $this->hasMany(TechnologyVersion::class);
    }
}
