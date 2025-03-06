<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Business;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Infrastructure\Aspects\HasC4ArchitectureRelations;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Zone;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class Actor extends IsKnownConfigurationItem
{
    use HasC4ArchitectureRelations, HasName;

    protected $table = 'actor';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function zones()
    {
        return $this->belongsToMany(Zone::class, 'actor_in_logical_zone', 'actor_id', 'logical_zone_id')->withPivot(['description']);
    }
}
