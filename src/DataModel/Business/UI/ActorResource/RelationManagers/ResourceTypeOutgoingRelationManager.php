<?php

namespace Swark\DataModel\Business\UI\ActorResource\RelationManagers;

class ResourceTypeOutgoingRelationManager extends C4ArchitectureRelationManager
{
    protected static ?string $otherEndLabel = 'To resource type';

    protected static ?string $title = 'Resource types';
    protected static string $recordSelectSearchColumns = 'resourceType.name';

    public function getInverseRelationshipName(): ?string
    {
        return "from" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "toResourceTypes";
    }
}
