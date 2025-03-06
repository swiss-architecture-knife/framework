<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Meta;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class RelationshipType extends IsKnownConfigurationItem
{
    protected $table = 'relationship_type';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'source_name',
        'target_name',
        'port',
        'protocol_stack_id',
        'is_restricting_types'
    ];

    public function relationships(): HasMany
    {
        return $this->hasMany(Relationship::class);
    }
}
