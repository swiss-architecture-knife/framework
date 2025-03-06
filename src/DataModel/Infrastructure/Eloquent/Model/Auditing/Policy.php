<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Auditing;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Chapter;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class Policy extends IsKnownConfigurationItem
{
    use HasName, HasDescription;

    protected $table = 'policy';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
    ];

    public function regulationChapters(): BelongsToMany
    {
        return $this->belongsToMany(Chapter::class, 'policy_for_regulation_chapter');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class)->orderBy('order_column');
    }
}
