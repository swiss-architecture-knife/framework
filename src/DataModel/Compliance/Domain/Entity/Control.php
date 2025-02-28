<?php

namespace Swark\DataModel\Compliance\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Auditing\Domain\Entity\Action;
use Swark\DataModel\Auditing\Domain\Entity\Finding;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;

class Control extends Model
{
    use HasName;

    protected $table = 'regulation_control';

    protected $fillable = [
        'external_id',
        'name',
        'content',
        'regulation_id',
        'regulation_chapter_id'
    ];

    public function regulation(): BelongsTo
    {
        return $this->belongsTo(Regulation::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'regulation_chapter_id');
    }

    public function findings(): BelongsToMany
    {
        return $this->belongsToMany(Finding::class, 'finding_for_control', 'regulation_control_id', 'finding_id');
    }

    public function actions(): MorphToMany
    {
        return $this->morphToMany(Action::class, 'actionable', 'action_assigned');
    }
}
