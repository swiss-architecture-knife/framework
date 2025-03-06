<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Swark\DataModel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Rule;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Scope;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\ScopedItem;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Actor;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\DataClassification;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class Zone extends IsKnownConfigurationItem
{
    use HasName, HasDescription, \Staudenmeir\EloquentHasManyDeep\HasRelationships, AssociatedWithOrganizations;

    protected $table = 'logical_zone';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'data_classification_id'
    ];

    public function actors()
    {
        return $this->belongsToMany(Actor::class, 'actor_in_logical_zone', 'logical_zone_id')->withPivot(['description']);
    }

    public function dataClassification(): BelongsTo
    {
        return $this->belongsTo(DataClassification::class, 'data_classification_id');
    }

    public function rules(): HasManyDeep
    {
        return $this->hasManyDeep(
            Rule::class,
            [ScopedItem::class, Scope::class],
            [['item_type', 'item_id'], 'id', 'id'],
            ['id', 'rule_scope_id', 'rule_id']
        );
    }
}
