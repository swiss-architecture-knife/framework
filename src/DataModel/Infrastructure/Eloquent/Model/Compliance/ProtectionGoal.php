<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Compliance;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\System;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class ProtectionGoal extends IsKnownConfigurationItem
{
    use HasName, HasDescription;

    protected $table = 'protection_goal';
    public $timestamps = false;

    protected $fillable = [
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
