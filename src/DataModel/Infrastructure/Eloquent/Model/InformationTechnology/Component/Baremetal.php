<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Swark\DataModel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\ManagedBaremetal;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\Nic;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class Baremetal extends IsKnownConfigurationItem
{
    use HasName, AssociatedWithOrganizations;

    protected $table = 'baremetal';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
    ];

    public function managed(): HasOne
    {
        return $this->hasOne(ManagedBaremetal::class);
    }

    public function nics(): MorphMany
    {
        return $this->morphMany(Nic::class, 'equipable');
    }
}
