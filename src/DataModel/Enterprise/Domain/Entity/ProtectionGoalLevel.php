<?php

namespace Swark\DataModel\Enterprise\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class ProtectionGoalLevel extends Model
{
    use HasScompId, HasName, HasDescription;

    protected $table = 'protection_goal_level';
    public $timestamps = false;

    protected $fillable = [
        'scomp_id',
        'name',
        'description',
        'position',
        'protection_goal_id',
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
