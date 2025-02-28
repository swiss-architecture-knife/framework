<?php

namespace Swark\DataModel\Business\UI\ActorResource\RelationManagers;

class ServiceIncomingRelationManager extends ServiceOutgoingRelationManager
{
    protected static ?string $otherEndLabel = 'From service';

    protected static Direction $direction = Direction::INCOMING;

    public function getInverseRelationshipName(): ?string
    {
        return "to" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "fromServices";
    }
}
