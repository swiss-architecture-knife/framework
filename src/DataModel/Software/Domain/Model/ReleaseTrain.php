<?php

namespace Swark\DataModel\Software\Domain\Model;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Swark\DataModel\Enterprise\Domain\Entity\System;
use Swark\DataModel\Software\Domain\Entity\Release;

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
