<?php

namespace Swark\Cms\Chapters;

use RecursiveIteratorIterator;

class Chapters
{

    private ?Item $rootItem = null;

    private ?array $flatItems = null;


    public function __construct(?array $flatItems = null) {
        $this->flatItems = $flatItems;
        $this->rootItem = Item::of(['__', 'Root', $flatItems], null, -1);
    }


    public function rootItem(): Item
    {
        return $this->rootItem;
    }

    public function items(): \Generator
    {
        return $this->rootItem->items();
    }

    public function isEmpty(): bool
    {
        return sizeof($this->rootItem->items) == 0;
    }

    public static function of(array $flatItems): Chapters
    {
        $r = new Chapters($flatItems);

        return $r;
    }

    /**
     * Create a new copy of this ToC on which you can work on
     * @return Chapters
     */
    public function copy(): Chapters
    {
        return static::of($this->flatItems);
    }

    public function find(string $id): ?Item
    {
        return $this->rootItem->find($id);
    }

    protected function recursiveEach(null|array $items = null)
    {
        foreach ($items as $item) {
            yield $item;

            if ($item->hasChildren()) {
                yield from $item->items();
            }
        }
    }

    public function iterate(): RecursiveIteratorIterator {
        if ($this->last == null) {
            $this->last = $this->iterator();
        }

        return $this->last;
    }

    private ?RecursiveIteratorIterator $last = null;
    public function each(): RecursiveIteratorIterator
    {
        return $this->iterate();
    }

    public function pull(): Item {
        $r = $this->next();

        // this might be the case when pull is programmatically called
        throw_if(!$r, exception: NoMoreChaptersException::class);

        return $r;
    }
    public function next(): ?Item {
        // we have to call next() on the first item
        $this->iterate()->next();
        $r = $this->iterate()->current();

        return $r;
    }

    public function reset(): void {
        $this->last = null;
    }

    public function iterator(): RecursiveIteratorIterator
    {
        $tocItemRecursiveIterator = new RecursiveChapterIterator($this->rootItem->itemsRaw());
        return new \RecursiveIteratorIterator($tocItemRecursiveIterator, \RecursiveIteratorIterator::SELF_FIRST);
    }
}

