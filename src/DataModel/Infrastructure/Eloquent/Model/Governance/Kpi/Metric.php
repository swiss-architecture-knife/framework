<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Kpi;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\System;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\SystemParameter;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class Metric extends IsKnownConfigurationItem
{
    use HasName, HasDescription;

    protected $table = 'metric';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'type',
        'precision',
        // higher (yes), lower (no)
        'goal_direction',
        // can be measured as a KPI
        'is_measurable',
        // can be used as a parameter for defining Business Continuity-related values for systems
        'is_system_parameter',
    ];

    public function kpis(): HasMany
    {
        return $this->hasMany(Kpi::class);
    }

    public function systemParameters(): HasMany
    {
        return $this->hasMany(SystemParameter::class);
    }

    public function systems(): BelongsToMany
    {
        return $this->belongsToMany(System::class, 'system_parameter')->withPivot(['value', 'description']);
    }
}
