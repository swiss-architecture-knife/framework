<?php

namespace Swark\Cms\Application;

use Illuminate\Support\Facades\View;
use Swark\Cms\Domain\Factory\PageFactory;
use Swark\Cms\Domain\Model\Block\Block;
use Swark\Cms\Domain\Model\Block\BlockCollection;
use Swark\Cms\Domain\Model\Block\BlockResolver;
use Swark\Cms\Domain\Model\Chapters\Chapters;
use Swark\Cms\Domain\Model\Page;
use Swark\Cms\Domain\Model\ResourceName;
use Swark\Cms\Domain\Model\Store\Search\Search;
use Swark\Cms\Domain\Model\Store\Search\Searchable;

class Cms
{
    public const VIEW_KEY_PAGE = 'page';

    public const VIEW_KEY_BLOCKS = 'blocks';

    public const VIEW_KEY_PAGE_CONFIG = 'page_config';

    private array $searchables = [];

    public function __construct(
        public readonly PageFactory $pageFactory,
        Searchable                  ...$searchables)
    {
        $this->searchables = $searchables;
    }

    public function commence(...$args): void
    {
        $page = $this->page(... $args);
        // share content in view for easier access
        View::share(static::VIEW_KEY_PAGE, $page);
        View::share(static::VIEW_KEY_BLOCKS, $page->blocks);
        View::share(static::VIEW_KEY_PAGE_CONFIG, $page->configuration);
    }

    /**
     * Create a new page
     * @param string $resourcePath
     * @param Chapters|array|null $chapters Either provide a chapter instance, an array for a new chapter or no chapter at all
     * @param array $fragments
     * @return Page
     */
    public function page(string $resourcePath, Chapters|array|null $chapters = null, array $fragments = []): Page
    {
        $blockCollection = new BlockCollection(new BlockResolver($this->searchables));
        $blockCollection->resolve([$resourcePath]);

        // fallback to an empty chapters array if no chapter is provided
        $useChapters = $chapters instanceof Chapters ? $chapters : Chapters::of($chapters ?? []);

        $page = $this->pageFactory->create($resourcePath, $blockCollection, $useChapters, $fragments);

        return $page;
    }

    public function findExactly(string $singleResourceName): Block
    {
        $blockCollection = new BlockCollection(new BlockResolver($this->searchables));
        return $blockCollection->get($singleResourceName);

    }

    public function load(array $sections = []): BlockCollection
    {
        $variantsBuilder = $this->search(Search::of(ResourceName::ofMany($sections)));
        $r = new BlockCollection($variantsBuilder);

        return $r;
    }
}
