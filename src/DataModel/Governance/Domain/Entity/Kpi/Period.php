<?php

namespace Swark\DataModel\Governance\Domain\Entity\Kpi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Period extends Model
{
    use HasName, HasScompId, HasDescription;

    protected $table = 'measurement_period';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'scomp_id',
        'begin_at',
        'end_at'
    ];

    public function measurements(): HasMany
    {
        return $this->hasMany(Measurement::class);
    }
}
