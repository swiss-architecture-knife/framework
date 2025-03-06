<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Governance;

use Illuminate\Support\Facades\DB;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class Criticality extends IsKnownConfigurationItem
{
    use HasName;

    protected $table = 'criticality';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'position'
    ];

    public static function findRange(): array
    {
        $r = DB::select("SELECT MIN(position) as l, MAX(position) AS r FROM criticality");

        return collect($r[0])->values()->toArray();
    }
}
