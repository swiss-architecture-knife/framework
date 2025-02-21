<?php

namespace Swark\DataModel\Strategy\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Strategy extends Model
{
    use HasName, HasScompId, HasDescription;

    protected $table = 'strategy';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'scomp_id',
        'description',
    ];

    public function objectives(): HasMany
    {
        return $this->hasMany(Objective::class);
    }


}
