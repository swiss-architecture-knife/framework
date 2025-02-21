<?php

namespace Swark\DataModel\Ecosystem\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class ArchitectureType extends Model
{
    protected $table = 'architecture_type';

    protected $fillable = [
        'scomp_id',
        'name',
    ];

    use HasScompId, HasName;
}
