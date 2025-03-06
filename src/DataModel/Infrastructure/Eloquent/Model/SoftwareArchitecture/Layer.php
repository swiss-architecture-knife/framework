<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class Layer extends IsKnownConfigurationItem
{
    use HasName;

    protected $table = 'logical_layer';

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    public function components(): BelongsToMany
    {
        return $this->belongsToMany(Component::class, 'component_in_layer', 'logical_layer_id', 'component_id');
    }
}
