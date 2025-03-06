<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Auditing;

use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\DataModel\Infrastructure\Repository\Scope\Scoping;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class Template extends IsKnownConfigurationItem
{
    use HasName, HasDescription;

    protected $table = 'scope_template';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'instance_of',
        'instance_parameters',
        'template_options',
    ];

    protected $casts = [
        'instance_parameters' => 'array',
        'template_options' => 'array'
    ];

    public function newScoper(mixed $queryOptions): Scoping
    {
        return new ($this->instance_of)(... [
                'instanceParameters' => $this->instance_parameters,
                'defaultQueryOptions' => $this->template_options,
                'customQueryOptions' => $queryOptions,
            ]
        );
    }
}
