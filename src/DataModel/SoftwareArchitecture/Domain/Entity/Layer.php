<?php

namespace Swark\DataModel\SoftwareArchitecture\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Layer extends Model
{
    protected $table = 'logical_layer';

    public $timestamps = false;

    protected $fillable = [
        'scomp_id',
        'name'
    ];

    public function components(): BelongsToMany
    {
        return $this->belongsToMany(Component::class, 'component_in_layer', 'logical_layer_id', 'component_id');
    }
}
