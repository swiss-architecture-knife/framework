<?php

namespace Swark\DataModel\Strategy\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Compliance\Domain\Entity\Chapter;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Question extends Model
{
    use HasName, HasScompId, HasDescription;

    protected $table = 'question';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'scomp_id',
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
