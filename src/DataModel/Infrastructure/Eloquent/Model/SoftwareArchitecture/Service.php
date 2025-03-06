<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Swark\DataModel\Infrastructure\Aspects\HasC4ArchitectureRelations;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\ProtocolStack;
use Swark\DataModel\Infrastructure\Eloquent\Model\IsKnownConfigurationItem;
use Swark\Kernel\Infrastructure\Aspects\HasDescription;

class Service extends IsKnownConfigurationItem
{
    use HasName, HasDescription, HasC4ArchitectureRelations;

    protected $table = 'service';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'component_id'
    ];

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    public function protocolStacks(): BelongsToMany
    {
        return $this->belongsToMany(ProtocolStack::class,
            'service_interface',
            'service_id',
        );
    }
}
