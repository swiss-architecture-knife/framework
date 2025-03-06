<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Governance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;

class Technology extends IsKnownConfigurationItem
{
    use HasName;

    protected $table = 'technology';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'type'
    ];

    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            TechnologyVersion::updateOrCreate([
                'technology_id' => $model->id,
                'is_latest' => true,
                'name' => 'latest',
            ]);
        });
    }

    public function versions(): HasMany
    {
        return $this->hasMany(TechnologyVersion::class);
    }

    public function latest(): TechnologyVersion
    {
        /** @var TechnologyVersion */
        return $this->versions()->where('is_latest', true)->first();
    }
}
