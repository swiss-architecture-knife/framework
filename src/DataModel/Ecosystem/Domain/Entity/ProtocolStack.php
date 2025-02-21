<?php

namespace Swark\DataModel\Ecosystem\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProtocolStack extends Model
{
    protected $table = 'protocol_stack';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'port',
        'application_layer_id',
        'presentation_layer_id',
        'session_layer_id',
        'transport_layer_id',
        'network_layer_id',
    ];

    public function applicationLayer(): BelongsTo
    {
        return $this->belongsTo(TechnologyVersion::class, 'application_layer_id');
    }

    public function presentationLayer(): BelongsTo
    {
        return $this->belongsTo(TechnologyVersion::class, 'presentation_layer_id');
    }

    public function sessionLayer(): BelongsTo
    {
        return $this->belongsTo(TechnologyVersion::class, 'session_layer_id');
    }

    public function transportLayer(): BelongsTo
    {
        return $this->belongsTo(TechnologyVersion::class, 'transport_layer_id');
    }

    public function networkLayer(): BelongsTo
    {
        return $this->belongsTo(TechnologyVersion::class, 'network_layer_id');
    }
}
