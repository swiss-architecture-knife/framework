<?php

namespace Swark\Services\Alm\Source;

use Dreitier\Alm\Source\Changelog;
use Dreitier\Alm\Source\ProvidesSources;
use Swark\DataModel\Software\Domain\Entity\Source;
use Swark\DataModel\Software\Domain\Entity\SourceProvider;
use Swark\DataModel\Software\Domain\Event\BeforeReleaseSaved;

/**
 * Resolve changelogs of sources based upon their SourceProvider
 * Do not forget to add this class to EventServiceProvider!
 */
class ChangelogResolver
{
    public function __construct(public readonly SourceProviderFactory $sourceProviderFactory)
    {
    }

    private array $cache = [];

    /**
     * Register SourceProvider in cache to reduce database calls
     *
     * @param int $softwareId
     * @param Source $source
     * @param ProvidesSources|SourceProvider $sourceProvider
     * @return void
     */
    public function register(int $softwareId, Source $source, ProvidesSources|SourceProvider $sourceProvider)
    {
        $keySoftwareId = (string)$softwareId;

        if (!isset($this->cache[$keySoftwareId])) {
            $keySourceId = (string)$source->id;

            if (!isset($this->cache[$keySoftwareId][$keySourceId])) {
                $this->cache[$keySoftwareId][$keySourceId] = [
                    $source,
                    $sourceProvider instanceof SourceProvider ? $this->sourceProviderFactory->get($sourceProvider) : $sourceProvider
                ];
            }
        }
    }

    /**
     * Return all registered Source to SourceProvider combinations for given software id
     * @param $softwareId
     * @return array
     */
    public function resolve($softwareId): array
    {
        if (isset($this->cache[(string)$softwareId])) {
            return array_values($this->cache[(string)$softwareId]);
        }

        return [];
    }

    /**
     * Before a release is saved, it will be checked if the changelog can be loaded from one of the sourceProviders with type 'changelog'
     * @param BeforeReleaseSaved $beforeReleaseSaved
     * @return void
     */
    public function handle(BeforeReleaseSaved $beforeReleaseSaved): void
    {
        $softwareId = $beforeReleaseSaved->release->software_id;

        // register source provider in cache if is not present yet
        if (!isset($this->cache[(string)$softwareId])) {
            Source::with(['sourceProvider'])
                ->where('software_id', $softwareId)
                ->where('type', SourceProvider::WELL_KNOWN_TYPE_CHANGELOG)
                ->each(fn($item) => $this->register($softwareId, $item, $this->sourceProviderFactory->get($item->sourceProvider)));
        }

        // foreach of the source to sourceProvider combinations, try to look up the changelog
        foreach ($this->resolve($softwareId) as $tupleOfSourceToSourceProvider) {
            /** @var Changelog $changelogResponse */
            $changelogResponse = $tupleOfSourceToSourceProvider[1]->changelog(...['source' => $tupleOfSourceToSourceProvider[0], 'version' => $beforeReleaseSaved->release->version]);

            // only overwrite value if it is present
            if (!empty($changelogResponse->url)) {
                $beforeReleaseSaved->release->changelog_url = $changelogResponse->url;
            }

            if (!empty($changelogResponse->content)) {
                $beforeReleaseSaved->release->changelog = $changelogResponse->content;
            }
        }
    }
}
