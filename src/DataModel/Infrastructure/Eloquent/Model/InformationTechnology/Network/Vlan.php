<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class Vlan extends IsKnownConfigurationItem
{
    use AssociatedWithOrganizations, HasName;

    protected $table = 'vlan';

    public $timestamps = false;

    protected $fillable = [
        'number',
        'name',
    ];

    public function toConfigurationItemName(): ?string
    {
        return null;
    }

    public function ipNetworks(): HasMany
    {
        return $this->hasMany(IpNetwork::class);
    }
}
