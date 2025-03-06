<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Domain\Model\InformationTechnology\Component\NoVirtualizationOptionOfParentHostException;
use Swark\DataModel\Domain\Model\InformationTechnology\Component\ParentHostEqualsCurrentHostException;
use Swark\DataModel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\Nic;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\ApplicationInstance;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Release;


class Host extends IsKnownConfigurationItem
{
    use HasName, AssociatedWithOrganizations;

    protected $table = 'host';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'operating_system_id',
        'virtualizer_id',
        'parent_host_id',
        'baremetal_id',
        'has_parent_host_affinity',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->parent_host_id) {
                throw_if($model->id == $model->parent_host_id, ParentHostEqualsCurrentHostException::class, 'This host equals the parent host');
                throw_if(Host::find($model->parent_host_id)->virtualizer_id == null, NoVirtualizationOptionOfParentHostException::class, 'Parent host must provide virtualization');
            }
        });
    }

    public function operatingSystem(): BelongsTo
    {
        return $this->belongsTo(Release::class, 'operating_system_id');
    }

    public function virtualizer(): BelongsTo
    {
        return $this->belongsTo(Release::class, 'virtualizer_id');
    }

    public function parentHost(): BelongsTo
    {
        return $this->belongsTo(Host::class, 'parent_host_id');
    }

    public function baremetal(): BelongsTo
    {
        return $this->belongsTo(Baremetal::class, 'baremetal_id');
    }

    public function executables(): MorphToMany
    {
        return $this->morphToMany(ApplicationInstance::class, 'executors');
    }

    public function nics(): MorphMany
    {
        return $this->morphMany(Nic::class, 'equipable');
    }
}
