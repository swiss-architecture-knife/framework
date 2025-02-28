<?php

namespace Swark\DataModel\Governance\Domain\Entity\Strategy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Auditing\Domain\Entity\Action;
use Swark\DataModel\Compliance\Domain\Entity\Chapter;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDisplay;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Objective extends Model
{
    use HasName, HasScompId, HasDescription, HasDisplay;

    protected $table = 'objective';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'scomp_id',
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
