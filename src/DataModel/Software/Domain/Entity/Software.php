<?php

namespace Swark\DataModel\Software\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Swark\DataModel\Business\Domain\Entity\Organization;
use Swark\DataModel\Enterprise\Domain\Entity\Criticality;
use Swark\DataModel\Enterprise\Domain\Entity\System;
use Swark\DataModel\Enterprise\Domain\Entity\Zone;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasC4ArchitectureRelations;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Software extends Model
{
    use HasScompId, HasName, HasC4ArchitectureRelations;

    protected $table = 'software';

    protected $fillable = [
        'scomp_id',
        'name',
        'business_criticality',
        'infrastructure_criticality',
        'usage_type',
        'is_virtualizer',
        'is_operating_system',
        'is_runtime',
        'is_library',
        'is_bundle',
        'logical_zone_id',
        'vendor_id',
        'artifact_type_id',
    ];

    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            Release::updateOrCreate([
                'software_id' => $model->id,
                'is_latest' => true,
                'is_any' => true,
                'version' => '*',
                'scomp_id' => 'any-latest'
            ]);
        });
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'vendor_id');
    }

    public function component(): MorphTo
    {
        return $this->morphTo(Component::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'logical_zone_id');
    }

    public function infrastructureCriticality(): BelongsTo
    {
        return $this->belongsTo(Criticality::class, 'infrastructure_criticality_id');
    }

    public function businessCriticality(): BelongsTo
    {
        return $this->belongsTo(Criticality::class, 'business_criticality_id');
    }

    public function artifactType(): BelongsTo
    {
        return $this->belongsTo(ArtifactType::class, 'artifact_type_id');
    }

    public function layers(): BelongsToMany
    {
        return $this->belongsToMany(Layer::class, 'software_in_layer', 'software_id', 'logical_layer_id');
    }

    public function systems(): MorphToMany
    {
        return $this->morphToMany(System::class, 'element', 'system_element');
    }

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }

    public function services(): HasManyThrough
    {
        return $this->hasManyThrough(Service::class, Component::class);
    }

    public function releases(): HasMany
    {
        return $this->hasMany(
            Release::class,
        );
    }

    public function latest(): Release
    {
        /** @var Release */
        return $this->releases()->where('is_latest', true)->first();
    }

    public function latestWithFixedVersion(): ?Release
    {
        /** @var Release */
        $r = $this->releases()->where('is_latest', true)->where('is_any', false)->first();

        // we could not find a 'latest' release, try to order by version
        if (!$r) {
            $r = $this->releases()->where('is_any', false)->orderBy('version', 'desc')->first();
        }

        return $r;
    }

    /**
     * World's worst similarity search engine.
     * @param $name
     * @param $namedType
     * @return mixed
     */
    public static function likeName($name, $namedType = 'software_name') {
        $query = <<<QUERY
SELECT s.* FROM (
    SELECT sw.*, 'ci_exact_type_and_name' AS name_source, 1.1 AS name_ranking, cin.name AS name_match FROM software sw
    LEFT JOIN configuration_item ci ON ci.ref_id = sw.id AND ci.ref_type = 'software'
    LEFT JOIN configuration_item_naming cin ON ci.id = cin.configuration_item_id
    LEFT JOIN naming_type nt ON cin.naming_type_id = nt.id
    WHERE nt.name = ?
            AND cin.name = ?
    UNION
    SELECT *, 'software_exact_name' AS name_source, 1.0 AS name_ranking, sw.name AS name_match FROM software sw WHERE name = ?
    UNION
    SELECT *, 'software_exact_scomp_id' AS name_source, 0.9 AS name_ranking, sw.scomp_id AS name_match FROM software sw WHERE scomp_id = ?
    UNION
    SELECT *, 'software_name_like' AS name_source, 0.4 AS name_ranking, sw.name AS name_match FROM software sw WHERE name LIKE ?
    UNION
    SELECT *, 'software_scomp_id_like' AS name_source, 0.3 AS name_ranking, sw.scomp_id AS name_match FROM software sw WHERE scomp_id LIKE ?
) s
ORDER BY name_ranking DESC;
QUERY;
        $wildcardName = '%' . $name . '%';
        $r = DB::select($query, [$namedType, $name, $name, $name, $wildcardName, $wildcardName]);

        return Software::hydrate($r);
    }

    public function sources(): HasMany
    {
        return $this->hasMany(
            Source::class
        );
    }
}
