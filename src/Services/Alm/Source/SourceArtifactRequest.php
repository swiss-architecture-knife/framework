<?php

namespace Swark\Services\Alm\Source;

use Dreitier\Alm\Source\Artifacts;
use Dreitier\Alm\Source\ProvidesSources;
use Swark\DataModel\Software\Domain\Entity\Source;

/**
 * Requests artifacts from a source provider.
 * This can e.g. be a GitLab instance, GitHub, artifacthub.io
 */
class SourceArtifactRequest
{
    /**
     * @param string $path Path in the source provider
     * @param string|null $referenceVersion Load artifact(s) based upon this version, commit etc.
     * @param ProvidesSources $sourceProvider Where to load artifacts from
     * @param array $context additional context data
     */
    public function __construct(
        public readonly ProvidesSources $sourceProvider,
        public readonly string          $path,
        public readonly ?string         $referenceVersion,
        public readonly array           $context = [],
    )
    {
    }

    private function buildArgs(...$args): array
    {
        $args['path'] = $args['path'] ?? $this->path;
        $args['reference'] = $this->referenceVersion;

        return $args;
    }

    /**
     * Find older source artifacts
     *
     * @param ...$args
     * @return Artifacts
     */
    public function findOlder(...$args): Artifacts
    {
        return $this->sourceProvider->findOlderThan(... $this->buildArgs(... $args));
    }

    /**
     * Find newer source artifacts
     * @param ...$args
     * @return Artifacts
     */
    public function findNewer(...$args): Artifacts
    {
        return $this->sourceProvider->findNewerThan(... $this->buildArgs(... $args));
    }

    /**
     * Find one or all artifacts, based upon the provided arguments.
     *
     * @param ...$args
     * @return Artifacts
     */
    public function find(...$args): Artifacts
    {
        return $this->sourceProvider->find(... $this->buildArgs(... $args));
    }

    /**
     * Create a new SourceArtifactRequest based upon the given parameter
     * @param Source $source
     * @param ProvidesSources $sourceProvider
     * @return SourceArtifactRequest
     */
    public static function of(Source $source, ProvidesSources $sourceProvider): SourceArtifactRequest
    {
        return new static(
            sourceProvider: $sourceProvider,
            path: $source->path,
            // we need a valid version
            referenceVersion: $source->software->latestWithFixedVersion()?->version,
            context: [
                'source' => $source,
                'software' => $source->software,
            ],
        );
    }
}
