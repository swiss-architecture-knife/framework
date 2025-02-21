<?php

namespace Swark\DataModel\Ecosystem\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;

class AdditionalNaming extends Model
{
    use HasName;

    protected $table = 'configuration_item_naming';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'configuration_item_id',
        'naming_type_id',
    ];

    public function configurationItem(): BelongsTo
    {
        return $this->belongsTo(ConfigurationItem::class);
    }

    public function namingType(): BelongsTo
    {
        return $this->belongsTo(NamingType::class);
    }
}
