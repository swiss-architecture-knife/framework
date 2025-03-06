<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Compliance;

use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\System;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class ProtectionGoalLevel extends IsKnownConfigurationItem
{
    use  HasName, HasDescription;

    protected $table = 'protection_goal_level';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'position',
        'protection_goal_id',
    ];

    public array $scompPathAttributes = [
        'protection_goal_id',
        'name'
    ];

    public function protectionGoal()
    {
        return $this->belongsTo(ProtectionGoal::class);
    }

    public function systems()
    {
        return $this->belongsToMany(System::class, 'system_in_protection_goal');
    }
}
