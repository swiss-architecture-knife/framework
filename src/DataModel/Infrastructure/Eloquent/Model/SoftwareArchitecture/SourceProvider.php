<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class SourceProvider extends IsKnownConfigurationItem
{
    use HasName;

    protected $table = 'source_provider';

    public $timestamps = false;

    // TODO Move to enum
    const WELL_KNOWN_TYPE_HELM = 'helm';
    const WELL_KNWON_TYPE_SOURCE = 'source';
    const WELL_KNOWN_TYPE_CHANGELOG = 'changelog';

    protected $fillable = [
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
