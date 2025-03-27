<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Meta;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Domain\Model\Meta\Direction;

class Relationship extends Model
{
    protected $table = 'relationship';

    public $timestamps = false;

    protected $fillable = [
        'source_type',
        'source_id',
        'source_name',
        'target_type',
        'target_id',
        'target_name',
        'relationship_type_id',
        'port',
        'protocol_stack_id',
        'description',
        'direction'
    ];

    protected $attributes = [
        'direction' => Direction::UNIDIRECTIONAL->value
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(RelationshipType::class);
    }
}
