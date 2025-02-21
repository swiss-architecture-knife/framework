<?php

namespace Swark\DataModel\Policy\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Rule extends Model
{
    use HasScompId, HasName, HasDescription;

    protected $table = 'rule';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'scomp_id',
        'description',
        'policy_id',
        'order_column'
    ];

    public function scopes(): HasMany
    {
        return $this->hasMany(Scope::class);
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class,);
    }
}
