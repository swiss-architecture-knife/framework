<?php

namespace Swark\DataModel\Kernel\Infrastructure\Repository;

/**
 * Based upon different cube you can map then into a nested object. In the end you receive an object with a nested hierarchy.
 * This is especially useful if you want to represent hierarchical data structures like trees
 */
class GroupBy
{
    public function __construct(
        public readonly GroupByTemplate $nestedTreeTemplate,
        public string                   $itemsProperty = 'items',
        public string                   $uniqueCounterName = '__cnt',
    )
    {
    }

    /**
     * Extract all types from the given range of elements
     * @param $type
     * @param int $idxBeginAt
     * @param int|null $idxEndAt
     * @return array
     */
    private function resolveItemsInRange($type, int $idxBeginAt = 0, ?int $idxEndAt = null): array
    {
        $r = [];
        $idxEndAt = $idxEndAt ?? sizeof($this->map) - 1;

        // in the given range of elements, look for the type
        for (; $idxBeginAt <= $idxEndAt; $idxBeginAt++) {
            if ($this->map[$idxBeginAt]['type'] === $type) {
                $r[] = ['begin' => $idxBeginAt, 'end' => null, 'item' => $this->map[$idxBeginAt]['properties']];
            }
        }

        // iterate over each element to fix the 'end' property based upon the previous beginnings
        for ($i = $m = sizeof($r) - 1; $i >= 0; $i--) {
            $r[$i]['end'] = $idxEndAt;

            if ($i != $m) {
                $r[$i]['end'] = $r[$i + 1]['begin'] - 1;
            }
        }

        return $r;
    }

    /**
     * Recursively iterate over the list of items and make range of items to look at smaller.
     *
     * @param $groupByTemplate
     * @param int $idxBeginAt
     * @param int|null $idxEndAt
     * @return array
     */
    private function iterateTreeTemplate(GroupByTemplate $groupByTemplate, int $idxBeginAt = 0, ?int $idxEndAt = null)
    {
        $r = [];

        $key = $groupByTemplate->groupName;
        $itemsOfType = $this->resolveItemsInRange($key, $idxBeginAt, $idxEndAt);

        foreach ($itemsOfType as $idx => $itemAtIndex) {
            $elem = [
                $key => $this->maybeAddUnique($key, $itemAtIndex['item'], $groupByTemplate->uniqueCounter),
                $this->itemsProperty => []
            ];

            $hideGroup = $groupByTemplate->hideGroup;

            if ($groupByTemplate->hasChild()) {
                $elem[$this->itemsProperty] = $this->iterateTreeTemplate($groupByTemplate->child(), $itemAtIndex['begin'], $itemAtIndex['end']);
            } else {
                $properties = $groupByTemplate->additionalProperties ?? [];

                // we do not need "items" property if we re-map the property field
                unset($elem[$this->itemsProperty]);

                // find all items in the given range and assign it to the sub property
                foreach ($properties as $property) {
                    $values = collect($this->resolveItemsInRange($property, $itemAtIndex['begin'], $itemAtIndex['end']))->map(fn($item) => $item['item'])->first();;
                    $elem[$property] = $this->maybeAddUnique($property, $values, $groupByTemplate->uniqueCounter);
                }
            }

            if ($hideGroup) {
                unset($elem[$key]);
            }

            $r[] = $elem;
        }

        return $r;
    }

    private array $uniques = [];

    private function maybeAddUnique(string $key, ?array $args = null, bool $addUniqueCounter = false): ?array
    {
        if (!$args) {
            return $args;
        }

        if ($addUniqueCounter) {
            if (!isset($this->uniques[$key])) {
                $this->uniques[$key] = 1;
            }

            $args[$this->uniqueCounterName] = $key . '_' . (++$this->uniques[$key]);
        }

        return $args;
    }

    private array $map = [];

    private array $alreadyGrouped = [];

    private ?array $requireGroupingFor = null;

    public function accept(string $groupName, mixed $id): bool
    {
        if (!$this->requireGroupingFor) {
            $groupByTemplate = $this->nestedTreeTemplate;
            $this->requireGroupingFor = [];

            do {
                $this->requireGroupingFor[$groupByTemplate->groupName] = $groupByTemplate->groupName;
            } while (($groupByTemplate = $groupByTemplate->child()) !== null);
        }

        if (isset($this->requireGroupingFor[$groupName])) {
            $key = $groupName . ":" . $id;

            if (isset($this->alreadyGrouped[$key])) {
                return false;
            }

            $this->alreadyGrouped[$key] = $key;
        }

        return true;
    }

    /**
     * Push a set of cubes to the nested object
     * @param array $data
     * @return void
     */
    public function push(array $data)
    {
        foreach ($data as $groupName => $properties) {

            if (!$this->accept($groupName, $properties['id'] ?? null)) {
                continue;
            }

            $this->map[] = ['type' => $groupName, 'properties' => $properties];
        }
    }

    public function toArray(): array
    {
        $r = $this->iterateTreeTemplate($this->nestedTreeTemplate);

        return $r;
    }

    public static function of(GroupByTemplate $groupByTemplate): GroupBy
    {
        return new static($groupByTemplate);
    }
}
