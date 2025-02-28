<?php

namespace Swark\Services\Alm\Source\Providers;

use Swark\DataModel\SoftwareArchitecture\Domain\Entity\SourceProvider;

class GithubSourceProvider implements ProvidesSources, ProvidesChangelog
{
    const WELL_KNOWN_NAME = 'github';

    public function __construct(private readonly SourceProvider $sourceProvider)
    {
    }

    public function find(...$args): Artifacts
    {
        // TODO: Implement find() method.
    }

    public function findNewerThan(...$args): Artifacts
    {
        // TODO: Implement findNewerThan() method.
    }

    public function findOlderThan(...$args): Artifacts
    {
        // TODO: Implement findOlderThan() method.
    }

    public function changelog(...$args): Changelog
    {
        $source = $args['source'];

        return new Changelog(
            raw: '',
            url: 'https://github.com/' . $source->path . '/releases/?q=' . $args['version'] . '&expanded=true',
            content: null,
        );
    }
}
