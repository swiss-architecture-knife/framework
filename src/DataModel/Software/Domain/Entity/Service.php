<?php

namespace Swark\DataModel\Software\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Swark\DataModel\Ecosystem\Domain\Entity\ProtocolStack;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasC4ArchitectureRelations;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Service extends Model
{
    use HasScompId, HasName, HasDescription, HasC4ArchitectureRelations;

    protected $table = 'service';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'scomp_id',
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
