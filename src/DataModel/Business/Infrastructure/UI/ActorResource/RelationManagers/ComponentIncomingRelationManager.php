<?php

namespace Swark\DataModel\Business\Infrastructure\UI\ActorResource\RelationManagers;

class ComponentIncomingRelationManager extends ComponentOutgoingRelationManager
{
    protected static ?string $otherEndLabel = 'From component';

    protected static Direction $direction = Direction::INCOMING;

    public function getInverseRelationshipName(): ?string
    {
        return "to" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "fromComponents";
    }
}
