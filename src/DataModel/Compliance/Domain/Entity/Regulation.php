<?php

namespace Swark\DataModel\Compliance\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regulation extends Model
{
    protected $table = 'regulation';

    protected $fillable = [
        'name',
        'scomp_id',
    ];

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }
}
