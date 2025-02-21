<?php

namespace Swark\DataModel\Scope\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;
use Swark\DataModel\Kernel\Infrastructure\Repository\Scope\Scoping;

class Template extends Model
{
    use HasScompId, HasName, HasDescription;

    protected $table = 'scope_template';

    public $timestamps = false;

    protected $fillable = [
        'scomp_id',
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
