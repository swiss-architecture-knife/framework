<?php

namespace Swark\DataModel\Business\UI\ActorResource\RelationManagers;

use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Software\Domain\Model\ServiceName;

class ServiceOutgoingRelationManager extends C4ArchitectureRelationManager
{
    protected static ?string $otherEndLabel = 'To service';

    protected static ?string $title = 'Services';
    protected static string $recordSelectSearchColumns = 'service.name';

    public function getInverseRelationshipName(): ?string
    {
        return "from" . class_basename($this->getOwnerRecord()) . "s";
    }

    public static function getRelationshipName(): string
    {
        return "toServices";
    }

    protected function createAttachAction(): Tables\Actions\AttachAction
    {
        return parent::createAttachAction()
            ->recordSelectOptionsQuery(function (Builder $query) {
                return $query
                    ->leftJoin('component', 'component.id', 'service.component_id')
                    ->leftJoin('software', 'software.id', 'component.software_id')
                    ->select([
                        'service.id',
                        'software.name AS software_name',
                        'component.name AS component_name',
                        'service.name AS service_name',
                    ])
                    ->orderBy('service.name')
                    ->orderBy('component.name')
                    ->orderBy('software.name');
            })
            ->recordTitle(function (Model $record): string {
                return ServiceName::from($record)->title();
            })
            ->recordSelectSearchColumns(['service.name', 'component.name', 'software.name']);
    }

    protected function nameTextColumn(): Tables\Columns\TextColumn
    {
        return parent::nameTextColumn()->formatStateUsing(fn(string $state, Model $record) => ServiceName::from($record)->title());
    }
}
