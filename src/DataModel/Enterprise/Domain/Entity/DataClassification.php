<?php

namespace Swark\DataModel\Enterprise\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class DataClassification extends Model
{
    use HasScompId, HasName, HasDescription;

    protected $table = 'data_classification';
    public $timestamps = false;

    protected $fillable = [
        'scomp_id',
        'name',
        'description'
    ];

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }
}
