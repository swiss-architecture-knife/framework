<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\ResourceType;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\Deployment;

class Resource extends IsKnownConfigurationItem
{
    protected $table = 'resource';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'resource_type_id',
        'provider_type',
        'provider_id',
    ];

    public array $scompPathAttributes = [
        'resource_type_id',
        'name',
    ];

    public function provider(): MorphTo
    {
        return $this->morphTo();
    }

    public function deployments(): MorphToMany
    {
        return $this->morphToMany(Deployment::class, 'element', 'deployment_element');
    }

    public function resourceType(): BelongsTo
    {
        return $this->belongsTo(ResourceType::class);
    }
}
