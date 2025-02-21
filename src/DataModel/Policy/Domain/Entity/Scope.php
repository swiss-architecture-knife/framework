<?php

namespace Swark\DataModel\Policy\Domain\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Scope\Domain\Entity\Template;

class Scope extends Model
{
    use HasName, HasDescription;

    protected $table = 'rule_scope';

    public $timestamps = false;

    protected $fillable = [
        'options',
        'scope_template_id',
        'rule_id'
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function detect()
    {
        $template = $this->scopeTemplate;

        $instance = ($template->newScoper($this->options));
        $scopedQuery = $instance->query();
        $scanResults = $scopedQuery->builder->get();

        $markItemIdAsSeen = [];

        // update each scan result as still found
        foreach ($scanResults as $scanResult) {
            $markItemIdAsSeen[] = $scanResult->id;
            ScopedItem::updateOrCreate(['item_id' => $scanResult->id, 'item_type' => $scopedQuery->modelType, 'rule_scope_id' => $this->id], ['last_found_at' => Carbon::now()]);
        }

        // mark each item (not already marked as missing) as newly missing
        ScopedItem::where('item_type', $scopedQuery->modelType)
            ->whereNull('first_missing_at')
            ->whereNotIn('item_id', $markItemIdAsSeen)
            ->update(['first_missing_at' => Carbon::now()]);
    }

    public function scopedItems(): HasMany
    {
        return $this->hasMany(ScopedItem::class,);
    }

    public function scopeTemplate(): BelongsTo
    {
        return $this->belongsTo(Template::class,);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class,);
    }
}
