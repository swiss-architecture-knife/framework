<?php

namespace Swark\DataModel\InformationTechnology\Domain\Entity\Component;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Swark\DataModel\InformationTechnology\Domain\Entity\Cloud\ManagedBaremetal;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\Nic;
use Swark\DataModel\Kernel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;
use Swark\DataModel\Kernel\Infrastructure\Aspects\IsConfigurationItem;

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
