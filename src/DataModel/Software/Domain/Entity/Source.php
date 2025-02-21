<?php

namespace Swark\DataModel\Software\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Source extends Model
{
    protected $table = 'source';

    public $timestamps = false;

    protected $fillable = [
        'type',
        'path',
        'options',
        'source_provider_id',
        'software_id',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public static function boot()
    {
        parent::boot();
    }

    public function sourceProvider(): BelongsTo
    {
        return $this->belongsTo(SourceProvider::class, 'source_provider_id');
    }

    public function software(): BelongsTo
    {
        return $this->belongsTo(Software::class);
    }
}
