<?php

namespace Swark\DataModel\Kernel\Infrastructure\Aspects;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Swark\DataModel\Ecosystem\Domain\Entity\AdditionalNaming;
use Swark\DataModel\Ecosystem\Domain\Entity\ConfigurationItem;

trait IsConfigurationItem
{
    public function configurationItem(): MorphOne
    {
        return $this->morphOne(ConfigurationItem::class, $this->getMorphClass(), 'ref_type', 'ref_id');
    }

    public function additionalNamings(): BelongsToMany
    {
        return $this->belongsToMany(AdditionalNaming::class, 'configuration_item', 'ref_id', 'id', 'id', 'configuration_item_id')
            ->where('ref_type', static::class);
    }
}
