<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Kpi;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class Period extends IsKnownConfigurationItem
{
    use HasName, HasDescription;

    protected $table = 'measurement_period';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'begin_at',
        'end_at'
    ];

    public function measurements(): HasMany
    {
        return $this->hasMany(Measurement::class);
    }
}
