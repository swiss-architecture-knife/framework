<?php

namespace Swark\DataModel\SoftwareArchitecture\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class SourceProvider extends Model
{
    use HasScompId, HasName;

    protected $table = 'source_provider';

    public $timestamps = false;

    const WELL_KNOWN_TYPE_HELM = 'helm';
    const WELL_KNWON_TYPE_SOURCE = 'source';
    const WELL_KNOWN_TYPE_CHANGELOG = 'changelog';

    protected $fillable = [
        'scomp_id',
        'name',
        'type',
        'path',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public static function boot()
    {
        parent::boot();
    }

    public function sources(): HasMany
    {
        return $this->hasMany(Source::class);
    }
}
