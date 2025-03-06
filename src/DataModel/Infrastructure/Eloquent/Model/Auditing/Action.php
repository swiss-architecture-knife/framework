<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Auditing;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Control;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy\Objective;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class Action extends IsKnownConfigurationItem
{
    use  HasName, HasDescription;

    public $timestamps = true;

    protected $table = 'action';

    protected $fillable = [
        'name',
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
