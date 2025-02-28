<?php

namespace Swark\DataModel\InformationTechnology\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Swark\DataModel\Auditing\Domain\Entity\Rule;
use Swark\DataModel\Auditing\Domain\Entity\Scope;
use Swark\DataModel\Auditing\Domain\Entity\ScopedItem;
use Swark\DataModel\Business\Domain\Entity\Actor;
use Swark\DataModel\Compliance\Domain\Entity\DataClassification;
use Swark\DataModel\Kernel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Zone extends Model
{
    use HasName, HasDescription, HasScompId, \Staudenmeir\EloquentHasManyDeep\HasRelationships, AssociatedWithOrganizations;

    protected $table = 'logical_zone';
    public $timestamps = false;

    protected $fillable = [
        'scomp_id',
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
