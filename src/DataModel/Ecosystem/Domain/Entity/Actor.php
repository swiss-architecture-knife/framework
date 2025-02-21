<?php

namespace Swark\DataModel\Ecosystem\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Enterprise\Domain\Entity\Zone;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasC4ArchitectureRelations;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Actor extends Model
{
    use HasC4ArchitectureRelations, HasName, HasScompId;

    protected $table = 'actor';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'scomp_id',
    ];

    public function zones()
    {
        return $this->belongsToMany(Zone::class, 'actor_in_logical_zone', 'actor_id', 'logical_zone_id')->withPivot(['description']);
    }
}
