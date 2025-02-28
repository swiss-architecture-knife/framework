<?php

namespace Swark\DataModel\Auditing\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;

class ScopedItem extends Model
{
    use  HasDescription;

    protected $table = 'rule_scope_item';

    public $timestamps = true;

    protected $fillable = [
        'item_id',
        'item_type',
        'rule_scope_id',
        'description',
        'first_missing_at',
        'last_found_at',
        'status'
    ];

    public function scope(): BelongsTo
    {
        return $this->belongsTo(Scope::class,);
    }

    public function item(): MorphTo
    {
        return $this->morphTo('item');
    }
}
