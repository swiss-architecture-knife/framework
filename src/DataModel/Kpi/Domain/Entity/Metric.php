<?php

namespace Swark\DataModel\Kpi\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Enterprise\Domain\Entity\Parameter;
use Swark\DataModel\Enterprise\Domain\Entity\System;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Metric extends Model
{
    use HasName, HasScompId, HasDescription;

    protected $table = 'metric';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'scomp_id',
        'description',
        'type',
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
        return $this->hasMany(Parameter::class);
    }

    public function systems(): BelongsToMany
    {
        return $this->belongsToMany(System::class, 'system_parameter')->withPivot(['value', 'description']);
    }
}
