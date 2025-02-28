<?php

namespace Swark\DataModel\SoftwareArchitecture\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class ArtifactType extends Model
{
    protected $table = 'artifact_type';

    use HasScompId, HasName;
}
