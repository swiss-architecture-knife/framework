<?php

namespace Swark\Services\Data;

use Swark\Services\Data\ResolvedScompType;
use Swark\Services\Data\ResolvedScompTypes;

class CompositeKeyContainer
{
    private array $map = [];

    public function set(string $type, string $key, string|int $value)
    {
        if (!isset($this->map[$type])) {
            $this->map[$type] = [];
        }

        $existingValue = null;
        $keyIsAlreadyInUse = isset($this->map[$type][$key]) && ($value !== ($existingValue = $this->map[$type][$key]));

        throw_if($keyIsAlreadyInUse, "Key $key is already in use for type $type (new value: $value, existing value: $existingValue)");

        $this->map[$type][$key] = $value;
    }

    public function get(string $type, string ...$keys)
    {
        $key = implode(":", $keys);

        throw_if(!isset($this->map[$type]), "Type '$type' does not exist. Has this imported before and has any value?");
        throw_if(!isset($this->map[$type][$key]), new AlreadyRegisteredException($key, $type, $this->map[$type]));

        return $this->map[$type][$key];
    }


    public function findScompIds(?string $references, array $inScompTypes, array $schema = []): ResolvedScompTypes
    {
        $items = [];
        $references = trim($references ?? '');

        if (!empty($references)) {
            $items = explode(",", $references);
        }

        $r = [];

        foreach ($items as $item) {
            $parts = explode(":", trim($item));

            throw_if(sizeof($parts) < 2, "Invalid scomp ID definition: '$item'. Expecting format {}:{}[,:{}...]");

            $tryScompId = $parts[0];

            $resolvedScompType = null;

            foreach ($inScompTypes as $checkScompType) {
                if (isset($this->map[$checkScompType][$tryScompId])) {
                    $resolvedScompType = new ResolvedScompType($checkScompType, $tryScompId, $this->get($checkScompType, $tryScompId));
                    break;
                }
            }

            throw_if(!$resolvedScompType, "Unable to resolve scomp ID $tryScompId in one of " . print_r($inScompTypes, true));

            $additionalParts = array_slice($parts, 1);

            $r[] = [
                $resolvedScompType, [
                    ... $additionalParts,
                    ... collect($schema)->flatMap(fn($item, $index) => [$item => isset($additionalParts[$index]) ? $additionalParts[$index] : null])->toArray(),
                ],
            ];
        }

        return ResolvedScompTypes::of($r);
    }

    public function idOrNull(string $scompType, string $scompId): ?int
    {
        if (isset($this->map[$scompType][$scompId])) {
            return $this->map[$scompType][$scompId];
        }

        return null;
    }
}
