<?php

namespace Swark\DataModel\Presenter\UI\Business\ActorResource\RelationManagers;

class ServiceIncomingRelationManager extends ServiceOutgoingRelationManager
{
    protected static ?string $otherEndLabel = 'From service';

    protected static InOut $direction = InOut::INCOMING;

    public function getInverseRelationshipName(): ?string
    {
        return "to" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "fromServices";
    }
}
