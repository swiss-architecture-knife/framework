<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Compliance;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Zone;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class DataClassification extends IsKnownConfigurationItem
{
    use  HasName, HasDescription;

    protected $table = 'data_classification';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description'
    ];

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }
}
