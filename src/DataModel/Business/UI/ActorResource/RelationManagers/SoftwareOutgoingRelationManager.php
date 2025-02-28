<?php

namespace Swark\DataModel\Business\UI\ActorResource\RelationManagers;

class SoftwareOutgoingRelationManager extends C4ArchitectureRelationManager
{
    protected static ?string $otherEndLabel = 'To software';

    protected static ?string $title = 'Software packages';
    protected static string $recordSelectSearchColumns = 'software.name';

    public function getInverseRelationshipName(): ?string
    {
        return "from" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "toSoftwares";
    }
}
