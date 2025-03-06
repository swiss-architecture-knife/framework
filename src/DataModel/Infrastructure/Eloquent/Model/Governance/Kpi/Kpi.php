<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Kpi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Action;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy\Objective;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class Kpi extends Model
{
    use HasName, HasDescription;

    protected $table = 'kpi';
    public $timestamps = false;

    protected $fillable = [
        'metric_id',
        'goal_value',
        'integer_threshold_1',
        'integer_threshold_2',
        'percentage_threshold_1',
        'percentage_threshold_2',
    ];

    public function metric(): BelongsTo
    {
        return $this->belongsTo(Metric::class);
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(Measurement::class);
    }

    public function objectives(): MorphToMany
    {
        return $this->morphedByMany(Objective::class, 'measurable', 'kpi_assigned');
    }

    public function actions(): MorphToMany
    {
        return $this->morphedByMany(Action::class, 'measurable', 'kpi_assigned');
    }
}
