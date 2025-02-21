<?php

namespace Swark\DataModel\Action\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Compliance\Domain\Entity\Control;
use Swark\DataModel\Compliance\Domain\Entity\Finding;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;
use Swark\DataModel\Strategy\Domain\Entity\Objective;

class Action extends Model
{
    use HasScompId, HasName, HasDescription;

    public $timestamps = true;

    protected $table = 'action';

    protected $fillable = [
        'name',
        'scomp_id',
        'description',
        'status',
        'begin_at',
        'end_at',
    ];

    protected $casts = [
        'begin_at' => 'date',
        'end_at' => 'date',
    ];

    public function controls(): MorphToMany
    {
        return $this->morphedByMany(Control::class, 'actionable', 'action_assigned');
    }

    public function objectives(): MorphToMany
    {
        return $this->morphedByMany(Objective::class, 'actionable', 'action_assigned');
    }

    public function findings(): MorphToMany
    {
        return $this->morphedByMany(Finding::class, 'actionable', 'action_assigned');
    }
}
