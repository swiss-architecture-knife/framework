<?php
namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\NicResource\Pages;

use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CreateChildNic extends CreateRelatedRecord {
    use NestedPage;

    protected static string $relationship = 'nics';

    protected function associateRecordWithParent(Model $record, Model $owner)
    {
        /** @var HasMany $relationship */
        if (($relationship = $this->getRelation()) instanceof HasMany) {
            $record->{$relationship->getForeignKeyName()} = $owner->getKey();
        }
        if (($relationship = $this->getRelation()) instanceof MorphMany) {
            $record->{$relationship->getForeignKeyName()} = $owner->getKey();
            // FIX for https://github.com/GuavaCZ/filament-nested-resources/issues/43
            $record->{$relationship->getMorphType()} = $owner->getMorphClass(); //Relation::getMorphedModel($alias); $owner::class;
        }

        return $record;
    }
}
