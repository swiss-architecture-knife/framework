<?php

namespace Swark\DataModel\Deployment\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Stage extends Model
{
    use HasScompId, HasName;

    protected $table = 'stage';

    public $timestamps = false;

    protected $fillable = [
        'scomp_id',
        'name'
    ];
}
