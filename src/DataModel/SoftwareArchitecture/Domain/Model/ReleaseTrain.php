<?php

namespace Swark\DataModel\SoftwareArchitecture\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\System;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Release;

class ReleaseTrain extends Model
{
    protected $table = 'release_train';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'is_latest',
        'system_id',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function releases(): BelongsToMany
    {
        return $this->belongsToMany(Release::class, 'release_in_release_train');
    }
}
