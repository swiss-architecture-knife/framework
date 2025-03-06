<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology;

use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class ArchitectureType extends IsKnownConfigurationItem
{
    use HasName;

    protected $table = 'architecture_type';

    protected $fillable = [
        'name',
    ];
}
