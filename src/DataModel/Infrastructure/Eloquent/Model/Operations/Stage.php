<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Operations;

use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\ConfigurationItem;

class Stage extends IsKnownConfigurationItem
{
    use HasName;

    protected $table = 'stage';

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];
}
