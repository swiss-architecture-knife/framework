<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Domain\Model\SoftwareArchitecture\ReleaseTrain;
use Swark\DataModel\Infrastructure\Aspects\HasC4ArchitectureRelations;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\ProtectionGoal;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Criticality;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Kpi\Metric;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\SystemParameter;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Zone;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\ResourceType;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\ApplicationInstance;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\Stage;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Software;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class System extends IsKnownConfigurationItem
{
    use HasName, HasDescription, HasC4ArchitectureRelations;

    protected $table = 'system';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'logical_zone_id',
        'stage_id',
        'business_criticality_id',
        'infrastructure_criticality_id',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'logical_zone_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class, 'stage_id');
    }

    public function infrastructureCriticality(): BelongsTo
    {
        return $this->belongsTo(Criticality::class, 'infrastructure_criticality_id');
    }

    public function businessCriticality(): BelongsTo
    {
        return $this->belongsTo(Criticality::class, 'business_criticality_id');
    }

    private function systemElement(string $related): MorphToMany
    {
        return $this->morphedByMany($related, 'element', 'system_element')->withPivot(['name', 'description']);
    }

    public function resourceTypes(): MorphToMany
    {
        return $this->systemElement(ResourceType::class);
    }

    public function softwares(): MorphToMany
    {
        return $this->systemElement(Software::class);
    }

    public function releaseTrains(): HasMany
    {
        return $this->hasMany(ReleaseTrain::class);
    }

    public function systemParameters(): BelongsToMany
    {
        return $this->belongsToMany(SystemParameter::class);
    }

    public function metrics(): BelongsToMany
    {
        return $this->belongsToMany(Metric::class, 'system_parameter')->withPivot(['value', 'description']);
    }

    public function protectionGoals(): BelongsToMany
    {
        return $this->belongsToMany(ProtectionGoal::class, 'system_in_protection_goal')->withPivot(['description', 'protection_goal_level_id']);
    }

    public function applicationInstances(): HasMany
    {
        return $this->hasMany(ApplicationInstance::class, 'system_id', 'id');
    }
}
