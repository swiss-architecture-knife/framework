<?php

namespace Swark\DataModel\Kernel\Infrastructure\Aspects;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Ecosystem\Domain\Entity\Organization;

trait AssociatedWithOrganizations
{
    public function associatedWithOrganizations(): MorphToMany
    {
        return $this->morphToMany(Organization::class, 'associatable', 'associated_with_organization')
            ->withPivot(['role']);
    }
}
