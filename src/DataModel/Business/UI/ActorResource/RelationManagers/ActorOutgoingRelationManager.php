<?php

namespace Swark\DataModel\Business\UI\ActorResource\RelationManagers;

class ActorOutgoingRelationManager extends C4ArchitectureRelationManager
{
    protected static ?string $otherEndLabel = 'To actor';

    protected static ?string $title = 'Actors';
    protected static string $recordSelectSearchColumns = 'actor.name';

    public function getInverseRelationshipName(): ?string
    {
        return "from" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "toActors";
    }
}
