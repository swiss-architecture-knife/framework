<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Auditing;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Control;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Criticality;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy\Objective;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class Finding extends IsKnownConfigurationItem
{
    use HasName, HasDescription;

    protected $table = 'finding';

    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'impact',
        'known_deficits',
        'probability',
        'extend_of_damage',
        'strategy',
        'criticality_id'
    ];

    public function criticality(): BelongsTo
    {
        return $this->belongsTo(Criticality::class);
    }

    public function controls(): MorphToMany
    {
        return $this->morphedByMany(Control::class, 'examinable', 'finding_assigned');
    }

    public function objectives(): MorphToMany
    {
        return $this->morphedByMany(Objective::class, 'examinable', 'finding_assigned');
    }
}
