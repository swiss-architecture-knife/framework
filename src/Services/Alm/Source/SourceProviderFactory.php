<?php

namespace Swark\Services\Alm\Source;

use Swark\DataModel\Software\Domain\Entity\SourceProvider;
use Swark\Services\Alm\Source\Providers\ArtifacthubSourceProvider;
use Swark\Services\Alm\Source\Providers\GithubSourceProvider;

/**
 * Factory class for source providers.
 * At the moment this is pretty static and only supports artifacthub.io and GitHub
 */
class SourceProviderFactory
{
    private array $cache = [];

    private function create(SourceProvider $sourceProvider)
    {
        // TODO make this configurable from outside
        return match ($sourceProvider->type) {
            ArtifacthubSourceProvider::WELL_KNOWN_NAME => new ArtifacthubSourceProvider($sourceProvider),
            GithubSourceProvider::WELL_KNOWN_NAME => new GithubSourceProvider($sourceProvider),
            default => throw_if(true, 'Unknown source provider with name ' . $sourceProvider->type),
        };
    }

    public function get(SourceProvider $sourceProvider)
    {
        if (!isset($this->cache['' . $sourceProvider->id])) {
            $this->cache['' . $sourceProvider->id] = $this->create($sourceProvider);
        }

        return $this->cache['' . $sourceProvider->id];
    }
}
