<?php

namespace Swark\Management\Architecture\Resources\Ecosystem\ActorResource\RelationManagers;

class ResourceTypeIncomingRelationManager extends ResourceTypeOutgoingRelationManager
{
    protected static ?string $otherEndLabel = 'From resource type';

    protected static Direction $direction = Direction::INCOMING;

    public function getInverseRelationshipName(): ?string
    {
        return "to" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "fromResourceTypes";
    }
}
