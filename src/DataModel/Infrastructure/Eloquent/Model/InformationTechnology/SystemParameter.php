<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Kpi\Metric;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\System;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class SystemParameter extends Model
{
    use HasDescription;

    protected $table = 'system_parameter';

    public $timestamps = false;

    protected $fillable = [
        'value',
        'description',
        'system_id',
        'metric_id',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class, 'system_id');
    }

    public function metric(): BelongsTo
    {
        return $this->belongsTo(Metric::class, 'metric_id');
    }
}
