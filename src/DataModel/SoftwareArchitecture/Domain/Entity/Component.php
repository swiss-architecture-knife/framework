<?php

namespace Swark\DataModel\SoftwareArchitecture\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Governance\Domain\Entity\TechnologyVersion;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasC4ArchitectureRelations;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Meta\Domain\Model\ProviderConsumerType;

class Component extends Model
{
    use HasName, HasC4ArchitectureRelations;

    protected $table = 'component';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'software_id',
    ];

    public function software(): BelongsTo
    {
        return $this->belongsTo(Software::class);
    }

    public function technologiesConsuming(): BelongsToMany
    {
        return $this->belongsToMany(TechnologyVersion::class, 'component_with_technology', 'component_id', 'technology_version_id')
            ->withPivotValue('provider_consumer_type', ProviderConsumerType::CONSUMER);
    }

    public function technologiesProducing(): BelongsToMany
    {
        return $this->belongsToMany(TechnologyVersion::class, 'component_with_technology', 'component_id', 'technology_version_id')
            ->withPivotValue('provider_consumer_type', ProviderConsumerType::PROVIDER);
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(TechnologyVersion::class, 'component_with_technology', 'component_id', 'technology_version_id');
    }

    public function layers(): BelongsToMany
    {
        return $this->belongsToMany(Layer::class, 'component_in_layer', 'component_id', 'logical_layer_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
