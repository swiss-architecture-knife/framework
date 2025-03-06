<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Infrastructure\Aspects\HasDisplay;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Action;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Chapter;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class Objective extends IsKnownConfigurationItem
{
    use HasName, HasDescription, HasDisplay;

    protected $table = 'objective';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'reason',
        'strategy_id',
    ];

    public function strategy(): BelongsTo
    {
        return $this->belongsTo(Strategy::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'question_in_objective');
    }

    /**
     * Objective deals with that regulation chapters
     * @return HasMany
     */
    public function regulationChapters(): BelongsToMany
    {
        return $this->belongsToMany(Chapter::class, 'objective_for_regulation_chapter');
    }

    public function actions(): MorphToMany
    {
        return $this->morphToMany(Action::class, 'actionable', 'action_assigned');
    }

    public function findings(): MorphToMany
    {
        return $this->morphToMany(Action::class, 'examinable', 'finding_assigned');
    }
}
