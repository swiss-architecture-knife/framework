<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model\Meta;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Ramsey\Uuid\UuidInterface;

/**
 * @property int $id
 * @property string $ref_type
 * @property string ref_id
 * @property UuidInterface $uuid
 * @property string $name
 * @property string $fullname
 * @property string $scomp_id
 * @property int $lifecycle_id
 */
final class ConfigurationItem extends Model
{
    protected $table = 'configuration_item';

    public $timestamps = true;
    protected $primaryKey = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'ref_type',
        'ref_id',
        'uuid',
        'name',
        'fullname',
        'scomp_id',
        'lifecycle_id',
        'last_seen_at'
    ];

    protected static function boot()
    {
        parent::boot();

        // auto-sets values on creation
        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    public static function expectByInternalId(string $clazz, int $id): ConfigurationItem
    {
        return static::byClassAndInternalId($clazz, $id)->firstOrFail();
    }

    public static function expectByScompId(string $clazz, int $id): ConfigurationItem
    {
        return static::byClassAndScompId($clazz, $id)->firstOrFail();
    }

    public function scopeByClassAndInternalId($query, string $clazz, int $id): void
    {
        $query->where('ref_type', (new $clazz)->getMorphClass())->where('ref_id', $id);
    }

    public function scopeByClassAndScompId($query, string $clazz, string $scompId): void
    {
        $query->where('ref_type', (new $clazz)->getMorphClass())->where('scomp_id', $scompId);
    }
}
