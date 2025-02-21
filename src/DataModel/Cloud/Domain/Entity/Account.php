<?php

namespace Swark\DataModel\Cloud\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Swark\DataModel\Ecosystem\Domain\Entity\Organization;
use Swark\DataModel\Infrastructure\Domain\Entity\Baremetal;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Account extends Model
{
    use HasScompId, HasName;

    protected $table = 'managed_account';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'scomp_id',
        'managed_service_provider_id',
    ];

    public function managedServiceProvider(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'managed_service_provider_id');
    }

    public function baremetals(): BelongsToMany
    {
        return $this->belongsToMany(Baremetal::class, 'managed_baremetal', 'managed_account_id', 'baremetal_id');
    }
}
