<?php

namespace Swark\DataModel\Meta\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class RelationshipType extends Model
{
    use HasScompId;

    protected $table = 'relationship_type';

    public $timestamps = false;

    protected $fillable = [
        'scomp_id',
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
