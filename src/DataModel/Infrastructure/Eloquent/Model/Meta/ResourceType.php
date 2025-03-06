<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Meta;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Swark\DataModel\Infrastructure\Aspects\HasC4ArchitectureRelations;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\TechnologyVersion;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\System;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class ResourceType extends IsKnownConfigurationItem
{
    use HasName, HasC4ArchitectureRelations;

    protected $table = 'resource_type';

    public $timestamps = false;

    protected $fillable = [
        'name',
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
