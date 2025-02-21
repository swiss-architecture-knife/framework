<?php

namespace Swark\DataModel\Enterprise\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class ProtectionGoal extends Model
{
    use HasScompId, HasName, HasDescription;

    protected $table = 'protection_goal';
    public $timestamps = false;

    protected $fillable = [
        'scomp_id',
        'name',
        'description'
    ];

    public function protectionGoalLevels(): HasMany
    {
        return $this->hasMany(ProtectionGoalLevel::class);
    }

    public function systems(): BelongsToMany
    {
        return $this->belongsToMany(System::class, 'system_in_protection_goal')->withPivot(['description', 'protection_goal_id']);
    }
}
