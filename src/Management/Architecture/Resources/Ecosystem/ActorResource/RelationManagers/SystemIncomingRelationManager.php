<?php

namespace Swark\Management\Architecture\Resources\Ecosystem\ActorResource\RelationManagers;

class SystemIncomingRelationManager extends SystemOutgoingRelationManager
{
    protected static ?string $otherEndLabel = 'From system';

    protected static Direction $direction = Direction::INCOMING;

    public function getInverseRelationshipName(): ?string
    {
        return "to" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "fromSystems";
    }
}
