<?php

namespace Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers;

class ComponentIncomingRelationManager extends ComponentOutgoingRelationManager
{
    protected static ?string $otherEndLabel = 'From component';

    protected static InOut $direction = InOut::INCOMING;

    public function getInverseRelationshipName(): ?string
    {
        return "to" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "fromComponents";
    }
}
