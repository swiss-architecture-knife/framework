<?php

namespace Swark\DataModel\Kpi\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;

class Measurement extends Model
{
    use HasDescription;

    protected $table = 'measurement';
    public $timestamps = true;

    protected $fillable = [
        'kpi_id',
        'measurement_period_id',
        'description',
        'goal_value',
        'current_value',
        'is_goal_reached',
    ];

    public static function boot()
    {
        parent::boot();
        self::saving(function ($model) {
            $model->goal_value = $model->kpi->goal_value;
            $direction = $model->kpi->metric->goal_direction;

            if ($direction == 'lower') {
                $model->is_goal_reached = $model->current_value <= $model->goal_value;
            }
            elseif ($direction == 'higher') {
                $model->is_goal_reached = $model->current_value >= $model->goal_value;
            }
        });
    }

    public function kpi(): BelongsTo
    {
        return $this->belongsTo(Kpi::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }
}
