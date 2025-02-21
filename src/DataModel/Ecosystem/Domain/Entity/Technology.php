<?php

namespace Swark\DataModel\Ecosystem\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Technology extends Model
{
    protected $table = 'technology';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'scomp_id',
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
                'scomp_id' => 'latest'
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
