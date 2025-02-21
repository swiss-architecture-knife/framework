<?php

namespace Swark\Cms\Chapters;

class Item
{
    public function __construct(
        public readonly ?Item  $parent,
        public readonly string $id,
        public readonly string $label,
        public readonly int    $depth = 0,
        public readonly int    $position = 1,
        public array           $items = [])
    {
    }

    public function hasChildren(): bool
    {
        return sizeof($this->items) > 0;
    }

    public function firstChild(): Item
    {
        return $this->items[0];
    }

    public function find(string $id): ?Item
    {
        foreach ($this->items as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }

        return null;
    }

    public function hasChildAt($index): bool
    {
        $r = sizeof($this->items) > $index;

        return $r;
    }

    public function flatten(): array
    {
        $items = [];

        foreach ($this->items as $item) {
            $items[] = $item->flatten();
        }

        return [$this->id, $this->label, $items];
    }

    public static function of(array $flatItem, ?Item $parent = null, int $depth = 0, int $position = 1)
    {
        $r = new Item($parent, $flatItem[0], $flatItem[1], $depth, $position);

        if (sizeof($flatItem) > 2 && is_array($flatItem[2])) {
            for ($i = 0, $m = sizeof($flatItem[2]); $i < $m; $i++) {
                $subItem = $flatItem[2][$i];
                $r->items[] = Item::of($subItem, $r, $r->depth + 1, $i + 1);
            }
        }

        return $r;
    }

    private ?string $uid = null;

    public function uid(): string {
        if ($this->uid === null) {
            $this->uid = implode("--", array_map(fn($item) => $item->id, $this->walkToRoot()));
        }

        return $this->uid;
    }

    public function walkToRoot(?callable $transformer = null): array {
        $items = [];
        $transformer = $transformer ?? fn($item) => $item;

        $item = $this;

        do {
            array_unshift($items, $transformer($item));

            $item = $item->parent;
        } while ($item && $item->depth >= 0);

        return $items;
    }

    public function itemsRaw(): array {
        return $this->items;
    }

    public function items(): \Generator
    {
        foreach ($this->items as $item) {
            yield $item;
        }
    }

    public function __toString() {
        return $this->label;
    }
}
