<?php

namespace Swark\DataModel\InformationTechnology\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Governance\Domain\Entity\Kpi\Metric;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\System;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;

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
