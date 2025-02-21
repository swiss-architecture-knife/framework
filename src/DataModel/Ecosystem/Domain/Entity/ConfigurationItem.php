<?php

namespace Swark\DataModel\Ecosystem\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class ConfigurationItem extends Model
{
    use HasScompId;

    protected $table = 'configuration_item';

    protected $fillable = [
        'scomp_id',
        'ref_type',
        'ref_id',
        'name',
        'fullname',
    ];
}
