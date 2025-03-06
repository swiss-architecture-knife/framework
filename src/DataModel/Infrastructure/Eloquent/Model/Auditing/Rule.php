<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Auditing;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class Rule extends IsKnownConfigurationItem
{
    use HasName, HasDescription;

    protected $table = 'rule';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'policy_id',
        'order_column'
    ];

    public array $scompPathAttributes = [
        'policy_id',
        'name',
    ];

    public function scopes(): HasMany
    {
        return $this->hasMany(Scope::class);
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }
}
