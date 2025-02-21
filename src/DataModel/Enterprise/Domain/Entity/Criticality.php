<?php

namespace Swark\DataModel\Enterprise\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Criticality extends Model
{
    use HasScompId, HasName;

    protected $table = 'criticality';
    public $timestamps = false;

    protected $fillable = [
        'scomp_id',
        'name',
        'position'
    ];

    public static function findRange(): array
    {
        $r = DB::select("SELECT MIN(position) as l, MAX(position) AS r FROM criticality");

        return collect($r[0])->values()->toArray();
    }
}
