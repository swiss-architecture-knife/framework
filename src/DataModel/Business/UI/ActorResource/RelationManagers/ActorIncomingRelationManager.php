<?php

namespace Swark\DataModel\Business\UI\ActorResource\RelationManagers;

class ActorIncomingRelationManager extends ActorOutgoingRelationManager
{
    protected static ?string $otherEndLabel = 'From actor';

    protected static Direction $direction = Direction::INCOMING;

    public function getInverseRelationshipName(): ?string
    {
        return "to" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "fromActors";
    }
}
