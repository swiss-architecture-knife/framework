<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class ArtifactType extends IsKnownConfigurationItem
{
    protected $table = 'artifact_type';

    use HasName;
}
