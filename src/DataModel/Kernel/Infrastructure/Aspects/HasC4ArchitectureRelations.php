<?php

namespace Swark\DataModel\Kernel\Infrastructure\Aspects;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Business\Domain\Entity\Actor;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\System;
use Swark\DataModel\Meta\Domain\Entity\ResourceType;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Component;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Service;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Software;

trait HasC4ArchitectureRelations
{

    private function relationship(string $type, string $relatedFrom, string $relatedTo)
    {
        return $this->morphToMany($relatedFrom, $type, 'relationship');
    }

    private function to(string $related)
    {
        return $this->morphToMany($related, 'target', 'relationship', 'source_id', 'target_id', 'id', 'id')
            ->withPivot(['direction', 'description', 'source_name', 'target_name', 'port', 'protocol_stack_id'])
            ->withPivotValue('target_type', (new static)->getMorphClass())
            ->withPivotValue('source_type', (new $related)->getMorphClass());
    }

    private function from(string $related)
    {
        return $this->morphToMany($related, 'source', 'relationship', 'target_id', 'source_id', 'id', 'id')
            ->withPivot(['direction', 'description', 'source_name', 'target_name', 'port', 'protocol_stack_id'])
            ->withPivotValue('target_type', (new $related)->getMorphClass())
            ->withPivotValue('source_type', (new static)->getMorphClass());
    }

    public function toActors(): MorphToMany
    {
        return $this->to(Actor::class);
    }

    public function fromActors(): MorphToMany
    {
        return $this->from(Actor::class);
    }

    public function toSystems(): MorphToMany
    {
        return $this->to(System::class);
    }

    public function fromSystems(): MorphToMany
    {
        return $this->from(System::class);
    }

    public function toSoftwares(): MorphToMany
    {
        return $this->to(Software::class);
    }

    public function fromSoftwares(): MorphToMany
    {
        return $this->from(Software::class);
    }

    public function toServices(): MorphToMany
    {
        return $this->to(Service::class);
    }

    public function fromServices(): MorphToMany
    {
        return $this->from(Service::class);
    }

    public function toComponents(): MorphToMany
    {
        return $this->to(Component::class);
    }

    public function fromComponents(): MorphToMany
    {
        return $this->from(Component::class);
    }

    public function toResourceTypes(): MorphToMany
    {
        return $this->to(ResourceType::class);
    }

    public function fromResourceTypes(): MorphToMany
    {
        return $this->from(ResourceType::class);
    }
}
