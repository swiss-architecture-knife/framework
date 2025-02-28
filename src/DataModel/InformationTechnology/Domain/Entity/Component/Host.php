<?php

namespace Swark\DataModel\InformationTechnology\Domain\Entity\Component;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Swark\DataModel\Deployment\Domain\Entity\ApplicationInstance;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\Nic;
use Swark\DataModel\Kernel\Infrastructure\Aspects\AssociatedWithOrganizations;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;
use Swark\DataModel\Kernel\Infrastructure\Aspects\IsConfigurationItem;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Release;


class Host extends Model
{
    use HasScompId, HasName, IsConfigurationItem, AssociatedWithOrganizations;

    protected $table = 'host';
    public $timestamps = true;

    protected $fillable = [
        'scomp_id',
        'name',
        'operating_system_id',
        'virtualizer_id',
        'parent_host_id',
        'baremetal_id',
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
