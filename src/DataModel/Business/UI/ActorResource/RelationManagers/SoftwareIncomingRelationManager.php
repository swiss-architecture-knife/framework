<?php

namespace Swark\DataModel\Business\UI\ActorResource\RelationManagers;

class SoftwareIncomingRelationManager extends SoftwareOutgoingRelationManager
{
    protected static ?string $otherEndLabel = 'From software package';

    protected static Direction $direction = Direction::INCOMING;

    public function getInverseRelationshipName(): ?string
    {
        return "to" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "fromSoftwares";
    }
}
