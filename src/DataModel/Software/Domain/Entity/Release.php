<?php

namespace Swark\DataModel\Software\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Swark\DataModel\Software\Domain\Event\BeforeReleaseSaved;
use Swark\DataModel\Software\Domain\Model\ReleaseTrain;

class Release extends Model
{
    protected $table = 'release';

    protected $fillable = [
        'scomp_id',
        'version',
        'software_id',
        'is_latest',
        'is_any',
        'changelog',
        'changelog_url',
    ];

    public static function boot()
    {
        parent::boot();

        parent::saving(function($model) {
            event(new BeforeReleaseSaved($model));
        });

        parent::saved(function($model) {
            // automatically set is_latest / is_any flag for all other releases of this software
            if ($model->is_latest) {
                Release::where('software_id', $model->software_id)->whereNot('id', $model->id)->update(['is_latest' => 0]);
            }

            if ($model->is_any) {
                Release::where('software_id', $model->software_id)->whereNot('id', $model->id)->update(['is_any' => 0]);
            }
        });
    }

    public function accepts(?Release $other): bool
    {
        // no other software but we have a fit
        if (!$other) {
            return false;
        }

        // other software
        if ($other->software_id != $this->software_id) {
            return false;
        }

        // this is a wildcard, match anything from other side
        if ($this->is_any) {
            return true;
        }

        return false;
    }

    public function software(): BelongsTo
    {
        return $this->belongsTo(Software::class);
    }

    public function releaseTrains(): BelongsToMany
    {
        return $this->belongsToMany(ReleaseTrain::class, 'release_in_release_train');
    }
}
