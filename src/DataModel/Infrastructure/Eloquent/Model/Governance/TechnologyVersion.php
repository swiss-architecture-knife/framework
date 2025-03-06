<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Governance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class TechnologyVersion extends IsKnownConfigurationItem
{
    use HasName;

    protected $table = 'technology_version';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'technology_id',
        'is_latest'
    ];

    public array $scompPathAttributes = [
        'technology_id',
        'name'
    ];

    public function createScompPathSegments(array $attributesToValues, ?Model $model = null): ?array {
        return [
            $attributesToValues['technology_id'],
            $attributesToValues['name']
        ];
    }

    public function technology(): BelongsTo
    {
        return $this->belongsTo(Technology::class);
    }

    public function resourceTypes(): HasMany
    {
        return $this->hasMany(TechnologyVersion::class);
    }
}
