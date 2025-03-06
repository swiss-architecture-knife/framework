<?php

namespace Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers;

class ResourceTypeIncomingRelationManager extends ResourceTypeOutgoingRelationManager
{
    protected static ?string $otherEndLabel = 'From resource type';

    protected static InOut $direction = InOut::INCOMING;

    public function getInverseRelationshipName(): ?string
    {
        return "to" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "fromResourceTypes";
    }
}
