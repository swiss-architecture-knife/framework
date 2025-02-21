<?php

namespace Swark\DataModel\Compliance\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Enterprise\Domain\Entity\Criticality;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;
use Swark\DataModel\Strategy\Domain\Entity\Objective;

class Finding extends Model
{
    use HasName, HasScompId, HasDescription;

    protected $table = 'finding';

    protected $fillable = [
        'name',
        'scomp_id',
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
