<?php

namespace Swark\Management\Architecture\Resources\Ecosystem\ActorResource\RelationManagers;

use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Software\Domain\Model\ComponentName;

class ComponentOutgoingRelationManager extends C4ArchitectureRelationManager
{
    protected static ?string $otherEndLabel = 'To component';

    protected static ?string $title = 'Components';
    protected static string $recordSelectSearchColumns = 'component.name';

    public function getInverseRelationshipName(): ?string
    {
        return "from" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "toComponents";
    }

    protected function createAttachAction(): Tables\Actions\AttachAction
    {
        return parent::createAttachAction()
            ->recordSelectOptionsQuery(function (Builder $query) {
                return $query
                    ->leftJoin('software', 'software.id', 'component.software_id')
                    ->select([
                        'component.id',
                        'software.name AS software_name',
                        'component.name AS component_name',
                    ])
                    ->orderBy('component.name')
                    ->orderBy('software.name');
            })
            ->recordTitle(function (Model $record): string {
                return ComponentName::from($record)->title();
            })
            ->recordSelectSearchColumns(['component.name', 'software.name']);
    }

    protected function nameTextColumn(): Tables\Columns\TextColumn
    {
        return parent::nameTextColumn()->formatStateUsing(fn(string $state, Model $record) => ComponentName::from($record)->title());
    }
}
