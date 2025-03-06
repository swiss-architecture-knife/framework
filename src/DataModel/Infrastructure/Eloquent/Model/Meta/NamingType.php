<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Meta;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Infrastructure\Aspects\HasName;

class NamingType extends Model
{
    use HasName;

    protected $table = 'naming_type';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'scomp_id',
        'public_format',
        'is_unique_in_type',
    ];
}
