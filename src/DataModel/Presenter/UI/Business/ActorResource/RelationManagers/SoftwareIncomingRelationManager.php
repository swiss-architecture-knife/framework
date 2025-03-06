<?php

namespace Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers;

class SoftwareIncomingRelationManager extends SoftwareOutgoingRelationManager
{
    protected static ?string $otherEndLabel = 'From software package';

    protected static InOut $direction = InOut::INCOMING;

    public function getInverseRelationshipName(): ?string
    {
        return "to" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "fromSoftwares";
    }
}
