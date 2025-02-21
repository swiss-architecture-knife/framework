<?php

namespace Swark\Services\Alm\Source\Providers;

use Composer\Semver\Comparator;
use Dreitier\Alm\Helm\Chart\Release;
use Dreitier\Alm\Source\Artifacts;
use Dreitier\Alm\Source\ProvidesSources;
use Dreitier\Alm\Versioning\Version;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Swark\DataModel\Software\Domain\Entity\SourceProvider;
use Swark\Services\Alm\Source\Events\HelmChartRetrievedEvent;
use Swark\Services\Alm\Source\Events\NewerHelmChartsFoundEvent;

class ArtifacthubSourceProvider implements ProvidesSources
{
    const WELL_KNOWN_NAME = 'artifacthub';

    public function __construct(public readonly SourceProvider $sourceProvider)
    {
    }

    protected function call(...$args): Artifacts
    {
        throw_if(!isset($args['path']), "Invalid call, argument 'path' is missing");

        $url = 'https://artifacthub.io/api/v1/packages/helm/' . $args['path'];

        if (isset($args['reference'])) {
            $url .= '/' . (string)$args['reference'];
        }

        Log::debug("Retrieving helm charts from $url");

        $content = Http::get($url);
        $raw = $content->json();
        $helmChartRelease = Release::ofArtifactoryHub($raw);

        event(new HelmChartRetrievedEvent($url, $helmChartRelease));

        return new Artifacts(
            raw: $raw,
            sourceArtifacts: [
                $helmChartRelease,
            ]
        );
    }

    public function find(...$args): Artifacts
    {
        return $this->call(... $args);
    }

    public function findNewerThan(...$args): Artifacts
    {
        throw_if(!isset($args['reference']), "'reference' must be set for ArtifacthubSourceProvider::findNewerThan()");

        $sources = $this->call(... $args);
        $newSources = [];

        /** @var Release $referenceHelmChart */
        $referenceHelmChart = $sources->first();

        $availableVersions = $referenceHelmChart->getAvailableVersions();
        $semverNewerOrEqualTo = $referenceHelmChart->chartVersion->comparable();

        event(new NewerHelmChartsFoundEvent($referenceHelmChart, $availableVersions));

        /** @var Version $availableVersion */
        foreach ($availableVersions as $availableVersion) {
            $semverAvailableVersion = $availableVersion->comparable();

            if ($semverNewerOrEqualTo
                && !Comparator::greaterThan($semverAvailableVersion, $semverNewerOrEqualTo)) {
                continue;
            }

            // use $availableVersion. This may be 5.41.9-distributed and differs from the $semverAvailableVersion
            $newSources[] = $this->find(...['path' => $args['path'], 'reference' => $availableVersion->raw])->first();
        }

        return new Artifacts($sources->raw, $newSources);
    }

    public function findOlderThan(...$args): Artifacts
    {
        throw_if(true, "Not implemented yet");
    }
}
