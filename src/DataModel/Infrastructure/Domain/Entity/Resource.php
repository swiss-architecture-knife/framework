<?php

namespace Swark\DataModel\Infrastructure\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Deployment\Domain\Entity\Deployment;
use Swark\DataModel\Ecosystem\Domain\Entity\ResourceType;

class Resource extends Model
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
