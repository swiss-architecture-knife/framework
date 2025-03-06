<?php

namespace Swark\DataModel\Infrastructure\Aspects;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Organization;

trait AssociatedWithOrganizations
{
    public function associatedWithOrganizations(): MorphToMany
    {
        return $this->morphToMany(Organization::class, 'associatable', 'associated_with_organization')
            ->withPivot(['role']);
    }
}
