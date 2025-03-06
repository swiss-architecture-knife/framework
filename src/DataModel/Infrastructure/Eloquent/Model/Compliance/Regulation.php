<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Compliance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class Regulation extends IsKnownConfigurationItem
{
    use HasName;

    protected $table = 'regulation';

    protected $fillable = [
        'name',
    ];

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }
}
