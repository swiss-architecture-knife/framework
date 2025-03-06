<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class Strategy extends IsKnownConfigurationItem
{
    use HasName, HasDescription;

    protected $table = 'strategy';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
    ];

    public function objectives(): HasMany
    {
        return $this->hasMany(Objective::class);
    }


}
