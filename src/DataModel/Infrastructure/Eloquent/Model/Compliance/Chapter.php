<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Compliance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Domain\Model\Auditing\TreatmentStrategy;
use Swark\DataModel\Domain\Model\Compliance\RelevanceType;
use Swark\DataModel\Infrastructure\Aspects\HasName;

class Chapter extends Model
{
    use HasName;

    protected $table = 'regulation_chapter';

    protected $fillable = [
        'external_id',
        'name',
        'official_content',
        'summary',
        'actual_status',
        'target_status',
        'relevancy',
        'regulation_id',
    ];

    protected $attributes = [
        'relevancy' => RelevanceType::LOW->value,
    ];

    public function regulation(): BelongsTo
    {
        return $this->belongsTo(Regulation::class);
    }
}
