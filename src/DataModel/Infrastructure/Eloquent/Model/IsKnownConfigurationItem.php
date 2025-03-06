<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Swark\DataModel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\AdditionalNaming;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\ConfigurationItem;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property ConfigurationItem $configurationItem
 */
class IsKnownConfigurationItem extends Model
{
    public function configurationItem(): MorphOne
    {
        return $this->morphOne(ConfigurationItem::class, $this->getMorphClass(), 'ref_type', 'ref_id');
    }

    public function additionalNamings(): BelongsToMany
    {
        return $this->belongsToMany(AdditionalNaming::class, 'configuration_item', 'ref_id', 'id', 'id', 'configuration_item_id')
            ->where('ref_type', static::class);
    }


    /**
     * @var string|null Attribute to use for creating the configuration item's name
     */
    public ?string $configurationItemNameAttribute = null;

    /**
     * Converts the model to a configuration item name, has precedence over $configurationItemNameAttribute
     * @return string|null
     */
    public function toConfigurationItemName(): ?string
    {
        return null;
    }

    /**
     * @var string|null Attribute to use for creating the configuration item's full name
     */
    public ?string $configurationItemFullNameAttribute = null;

    /**
     * Converts the model to a configuration item full name, has precedence over $configurationItemFullNameAttribute
     * @return string|null
     */
    public function toConfigurationItemFullName(): ?string
    {
        return null;
    }

    public array $scompPathAttributes = [];


    protected function createScompPathSegments(array $attributesToValues, ?Model $model): ?array
    {
        return null;
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            // for newly created configuration items, we have to derive the ScompID based upon its attribute or name
            $scompId = $model->deriveScompId($model);
            // and then have to store the configuration item
            $model->upsertConfigurationItem($scompId);
        });

        static::saved(function ($model) {
            // for already existing configuration items, we are upserting its values
            if (!$model->wasRecentlyCreated) {
                $model->upsertConfigurationItem();
            }
        });
    }

    /**
     * Based upon the parameters, a scomp id is derived.
     * That scomp ID is unique for a configuration item type.
     *
     * @param string|Model|array $modelOrAttributesToValue
     * @return ScompId
     */
    protected function deriveScompId(string|Model|array $modelOrAttributesToValue): ScompId
    {
        if (is_string($modelOrAttributesToValue)) {
            return new ScompId([$modelOrAttributesToValue]);
        }

        $attributesToValue = is_array($modelOrAttributesToValue) ? $modelOrAttributesToValue : $modelOrAttributesToValue->toArray();
        $model = is_object($modelOrAttributesToValue) ? $modelOrAttributesToValue : null;

        $scompIdCandidate = new ScompId($this->scompIdStrategy($attributesToValue, $model));
        // at this point in time, the derived scomp ID may already exist for another configuration item of the same type.
        // this can be the case if there has been a configuration item in the past, and its model has been deleted - but not the configuration item itself.
        // that behavior is totally acceptable.

        // anyways, if we have a model instance (= newly generated model) we may have to randomize the scomp ID
        if ($model) {
            $scompIdCandidate = $this->maybeRandomize($model, $scompIdCandidate);
        }

        return $scompIdCandidate;
    }

    /**
     * Randomizes the scomp ID if its already in use for another configuration item of the same configuration item type.
     *
     * @param Model $model
     * @param ScompId $scompId
     * @param int $tries
     * @return ScompId
     * @throws \Throwable
     */
    private function maybeRandomize(Model $model, ScompId $scompId, int $tries = 0): ScompId
    {
        throw_if($tries > 5, "We were not able to create a randomized scomp ID after 5 tries");
        $maybeExistingConfigurationItem = ConfigurationItem::byClassAndScompId($model::class, $scompId)->first();

        // there is already a configuration item present with that name
        if ($maybeExistingConfigurationItem && ($maybeExistingConfigurationItem->id != $model->id)) {
            $rand = Str::lower(Str::random(8));

            if ($tries == 0) {
                $scompId = $scompId->append($rand);
            } else {
                $scompId = $scompId->replaceLast($rand);
            }

            return $this->maybeRandomize($model, $scompId, ++$tries);
        }

        return $scompId;
    }

    /**
     * Create a new -un-randomized- scomp ID passed upon the models parameters.
     *
     * @param array $attributesToValue
     * @param Model|null $model
     * @return array
     * @throws \Throwable
     */
    private function scompIdStrategy(array $attributesToValue, ?Model $model): array
    {
        // 1. prefer custom method to create attributes by ourselves
        if ($r = $this->createScompPathSegments($attributesToValue, $model)) {
            return $r;
        }

        // 2. Prefer scomPathAttributes
        if (!empty($this->scompPathAttributes)) {
            $r = [];

            foreach ($this->scompPathAttributes as $attribute) {
                throw_if(!isset($attributesToValue[$attribute]), "You are referencing attribute '$attribute' but this is not available as value");
                $r[] = $attributesToValue[$attribute];
            }

            return $r;
        }

        // 3. Fallback to HasName

        // When a well-known configuration item implements HasName, we can use that to infer the scompid
        if (in_array(HasName::class, class_uses_recursive(static::class))) {
            throw_if(!isset($attributesToValue['name']), "Cannot infer scomp ID by name, args: ");
            return [$attributesToValue['name']];
        }

        // 4. otherwise we take try to make the best guess for a scomp id
        // try to generate a scomp ID by child class, otherwise fall back to concatenating all primary attribute values
        return [implode('_', array_values($attributesToValue))];
    }

    /**
     * Upserts the configuration item's name, fullname and maybe scomp ID
     * @param string|null $setScompIdTo
     * @return void
     */
    private function upsertConfigurationItem(?string $setScompIdTo = null): void
    {
        $names = $this->generateConfigurationItemNames($this);

        $configurationItem = $this->configurationItem ?? new ConfigurationItem();
        $configurationItem->name = $names[0];
        $configurationItem->fullname = $names[1];

        if ($setScompIdTo) {
            $configurationItem->scomp_id = $setScompIdTo;
        }

        $this->configurationItem = $this->configurationItem()->save($configurationItem);
    }

    /**
     * Update or creates a configuration item by its scomp ID
     * @param string $scompId
     * @param array $attributes
     * @param array $values
     * @return $this
     */
    public static function upsert(string|array $scompIdOrAttributes, array $attributesOrValues = [], array $values = []): Model
    {
        // based upon the parameters, derive the values to use
        $scompId = $scompIdOrAttributes;
        $attributes = $attributesOrValues;

        if (is_array($scompIdOrAttributes)) {
            $scompId = (new static)->deriveScompId(array_merge($scompIdOrAttributes, $attributesOrValues));

            $attributes = $scompIdOrAttributes;
            $values = $attributesOrValues;
        }

        // logic for upserting the model. it is defined as a lambda so we may run it inside a transaction
        $updateOrCreateLambda = function () use ($scompId, $attributes, $values) {
            $existingModelByScompId = static::scompId($scompId)->first();

            if ($existingModelByScompId) {
                $values = array_merge($attributes, $values);
                $attributes = ['id' => $existingModelByScompId->id];
            }

            $item = static::updateOrCreate($attributes, $values);

            $modelHasBeenCreatedWithTotallyNewScompId = $item->wasRecentlyCreated && !$existingModelByScompId;
            $existingModelHasBeenUpdatedAndWantsToChangeScompId = !$item->wasRecentlyCreated && !$existingModelByScompId;

            if ($existingModelHasBeenUpdatedAndWantsToChangeScompId || $modelHasBeenCreatedWithTotallyNewScompId) {
                $item->configurationItem->scomp_id = $scompId;
                $item->configurationItem->save();
            }

            return $item;
        };

        // it is important that this method runs in a transaction.
        // Otherwise we could have stored items in the model table without having the correct scomp ID
        if (DB::transactionLevel() > 0) {
            return $updateOrCreateLambda();
        } else {
            return DB::transaction($updateOrCreateLambda);
        }
    }

    /**
     * Generates the configuration item names. Those names are basically used entrypoint and make everything searchable.
     *
     * @param $configurationItem
     * @return array
     */
    protected function generateConfigurationItemNames($configurationItem): array
    {
        $useConfigurationItemName = $configurationItem->toConfigurationItemName() ?? (($configurationItem->configurationItemNameAttribute !== null) ? $configurationItem->{$configurationItem->configurationItemNameAttribute} : null);
        $useConfigurationItemFullName = $configurationItem->toConfigurationItemFullName() ?? (($configurationItem->configurationItemFullNameAttribute !== null) ? $configurationItem->{$configurationItem->configurationItemFullNameAttribute} : null);

        if (!$useConfigurationItemName) {
            if (in_array(HasName::class, class_uses_recursive(static::class))) {
                $useConfigurationItemName = $configurationItem->name;
            }
        }

        return [$useConfigurationItemName, $useConfigurationItemFullName ?? $useConfigurationItemName];
    }

    /**
     * Updates the scomp ID of this configuration item
     *
     * @param int $id
     * @param string $newScompId
     * @return void
     * @throws \Throwable
     */
    public static function patchScompId(int $id, string $newScompId)
    {
        $existingModelByScompId = static::scompIdV2($newScompId)->first();

        throw_if($existingModelByScompId && $existingModelByScompId->id !== $id, "There is already another item of type {$existingModelByScompId->getMorphClass()} registered with scomp name '{$newScompId}'.");
        DB::statement('UPDATE configuration_item SET scomp_id = ? WHERE ref_type = ? AND ref_id = ?', [
            $newScompId, $existingModelByScompId->getMorphClass(), $id
        ]);
    }

    /**
     * Find a model based upon its scomp id
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $scompId
     */
    public function scopeScompId($query, string|array $scompId): void
    {

        $query->whereIn('id', DB::table('configuration_item')->select('ref_id')
            ->where(function (QueryBuilder $query) use ($scompId) {
                $query->where('ref_type', (new static())->getMorphClass());
                if (is_array($scompId)) {
                    $query->whereIn('scomp_id', $scompId);
                } else {
                    $query->where('scomp_id', $scompId);
                }
            }));
    }

    /**
     * Find a model based upon its UUID
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $scompId
     */
    public
    function scopeUuid($query, $uuid): void
    {
        $query->whereIn('id', DB::table('configuration_item')->select('ref_id')
            ->where('uuid', $uuid)
            ->where('ref_type', (new static())->getMorphClass()));
    }

    /**
     * Requires that a model with the given scomp id does exist
     * @param $scompId
     * @return $this
     */
    public static function byScompId($scompId): Model
    {
        $r = static::scompId($scompId)->firstOrFail();

        return $r;
    }

    public static function toScompId(array|string $scompifiable): string
    {
        $scompifiable = !is_array($scompifiable) ? [$scompifiable] : $scompifiable;
        return Str::snake(Str::lower(implode(":", $scompifiable)));
    }
}
