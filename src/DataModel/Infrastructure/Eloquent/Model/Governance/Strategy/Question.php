<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Chapter;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class Question extends IsKnownConfigurationItem
{
    use HasName, HasDescription;

    protected $table = 'question';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
    ];

    public function strategies(): BelongsToMany
    {
        return $this->belongsToMany(Strategy::class, 'question_in_strategy');
    }

    public function objectives(): BelongsToMany
    {
        return $this->belongsToMany(Objective::class, 'question_in_objective');
    }

    /**
     * Question occurs because of that regulation chapter
     * @return HasMany
     */
    public function regulationChapters(): BelongsToMany
    {
        return $this->belongsToMany(Chapter::class, 'question_for_regulation_chapter');
    }
}
