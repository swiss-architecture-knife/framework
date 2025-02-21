<?php

namespace Swark\DataModel\Infrastructure\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Swark\DataModel\Cloud\Domain\Entity\ManagedBaremetal;
use Swark\DataModel\Kernel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;
use Swark\DataModel\Kernel\Infrastructure\Aspects\IsConfigurationItem;
use Swark\DataModel\Network\Domain\Entity\Nic;

class Baremetal extends Model
{
    use HasScompId, HasName, IsConfigurationItem, AssociatedWithOrganizations;

    protected $table = 'baremetal';

    public $timestamps = true;

    protected $fillable = [
        'scomp_id',
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
