<?php

namespace Swark\DataModel\Meta\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Swark\DataModel\Governance\Domain\Entity\TechnologyVersion;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\System;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasC4ArchitectureRelations;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class ResourceType extends Model
{
    use HasScompId, HasName, HasC4ArchitectureRelations;

    protected $table = 'resource_type';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'scomp_id',
        'technology_version_id'
    ];

    public function technologyVersion(): BelongsTo
    {
        return $this->belongsTo(TechnologyVersion::class);
    }

    public function systems(): MorphToMany
    {
        return $this->morphToMany(System::class, 'element', 'system_element');
    }

    public function scopeInUse($query) {
        return $query->whereIn('id', function($query) {
            $query->select('resource_type_id')->from('resource')->having(DB::raw('COUNT(*)'), '>', 0)->groupBy('resource_type_id');
        });
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('name', $value)->firstOrFail();
    }
}
