<?php

namespace Swark\Management\Architecture\Resources\Ecosystem\ActorResource\RelationManagers;

class SystemOutgoingRelationManager extends C4ArchitectureRelationManager
{
    protected static ?string $otherEndLabel = 'To system';

    protected static ?string $title = 'Systems';
    protected static string $recordSelectSearchColumns = 'system.name';

    public function getInverseRelationshipName(): ?string
    {
        return "from" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "toSystems";
    }
}
