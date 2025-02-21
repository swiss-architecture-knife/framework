<?php

namespace Swark\DataModel\Compliance\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;

class Chapter extends Model
{
    use HasName;

    protected $table = 'regulation_chapter';

    protected $fillable = [
        'name',
        'external_id',
        'summary',
        'official_content',
        'actual_status',
        'target_status',
        'relevancy',
        'regulation_id',
    ];

    public function regulation(): BelongsTo
    {
        return $this->belongsTo(Regulation::class);
    }
}
