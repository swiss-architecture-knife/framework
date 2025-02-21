<?php

namespace Swark\DataModel\Kpi\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Action\Domain\Entity\Action;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Strategy\Domain\Entity\Objective;

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
