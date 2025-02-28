<?php

namespace Swark\DataModel\Meta\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class NamingType extends Model
{
    use HasName, HasScompId;

    protected $table = 'naming_type';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'scomp_id',
        'public_format',
        'is_unique_in_type',
    ];
}
