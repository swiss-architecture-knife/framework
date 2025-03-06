<?php

namespace Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers;

class SystemIncomingRelationManager extends SystemOutgoingRelationManager
{
    protected static ?string $otherEndLabel = 'From system';

    protected static InOut $direction = InOut::INCOMING;

    public function getInverseRelationshipName(): ?string
    {
        return "to" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "fromSystems";
    }
}
