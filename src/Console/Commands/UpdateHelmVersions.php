<?php
declare(strict_types=1);

namespace Swark\Console\Commands;

use Dreitier\Alm\Inspecting\Helm\Chart\Release as HelmChartRelease;
use Dreitier\Alm\Inspecting\Source\Artifacts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Release;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Software;
use Swark\Services\Alm\Source\ChangelogResolver;
use Swark\Services\Alm\Source\Events\HelmChartRetrievedEvent;
use Swark\Services\Alm\Source\Events\NewerHelmChartsFoundEvent;
use Swark\Services\Alm\Source\SourceArtifactRequest;
use Swark\Services\Alm\Source\SourceProviderFactory;

class UpdateHelmVersions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helm:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load version differences between current release in swark and available updates on artifacthub.io';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sourceProviderFactory = (new SourceProviderFactory());

        // find all softwares with type 'helm'
        $softwares = Software::with(['sources', 'sources.sourceProvider'])->whereIn('id', function ($query) {
            return $query->select('software_id')->from('source')->where('type', 'helm');
        })->get();

        Event::listen(function (HelmChartRetrievedEvent $event) {
            $this->info('    Retrieved ' . $event->helmChartRelease->chartVersion->raw);
        });

        Event::listen(function (NewerHelmChartsFoundEvent $event) {
            $this->info('  Latest Helm chart is: ' . $event->helmChartRelease->chartVersion->raw);
            $this->info('  There are ' . sizeof($event->availableVersion) . " releases registered. Checking if newer releases exists...");
        });

        foreach ($softwares as $software) {
            $helmSource = $software->sources->where(fn($item) => $item->type == 'helm')->first();
            $changelogSource = $software->sources->where(fn($item) => $item->type == 'changelog')->first();

            if ($changelogSource) {
                app(ChangelogResolver::class)->register($software->id, $changelogSource, $changelogSource->sourceProvider);
            }

            $this->info("Trying to resolve Helm chart for {$software->name} from {$helmSource->type}:{$helmSource->path}...");
            $artifacthubProvider = $sourceProviderFactory->get($helmSource->sourceProvider);
            $sourceRequest = SourceArtifactRequest::of($helmSource, $artifacthubProvider);

            $this->info("  Highest local reference version: " . $sourceRequest->referenceVersion);

            /** @var Artifacts $availableReleases */
            $availableReleases = null;

            // if software has no releases or just "any" release, find only latest release
            if (!$sourceRequest->referenceVersion) {
                $availableReleases = $sourceRequest->find();
            } else {
                $this->info("  Finding newer versions of Helm chart");

                $availableReleases = $sourceRequest->findNewer();
            }

            $finalSoftwareCandidate = null;
            $namingType = 'helm-chart-application-reference';

            /** @var HelmChartRelease $first */
            if ($first = $availableReleases->first()) {
                $applicationName = $first->application->name;
                $this->info('  Application name inside this helm: ' . $applicationName);

                $softwareCandidates = Software::likeName($applicationName);

                foreach ($softwareCandidates as $softwareCandidate) {
                    $this->info('    Software catalog item ' . $softwareCandidate->id . ' with name "' . $softwareCandidate->name_match . '" matches to ' . $softwareCandidate->name_ranking) . ' percent';

                    if ($softwareCandidate->name_ranking > 0.5 && (!$finalSoftwareCandidate || ($finalSoftwareCandidate->name_ranking < $softwareCandidate->name_ranking))) {
                        $finalSoftwareCandidate = $softwareCandidate;
                    }
                }

                if (!$finalSoftwareCandidate) {
                    $this->warn('    None of the software catalog items has a name similarity of at least 0.5. Do one of this:');
                    $this->warn('    - Create/update a software with name "' . $applicationName . '"');
                    $this->warn('    - Create/update a software with scomp ID "' . $applicationName . '"');
                    $this->warn('    - For the CI naming type "' . $namingType . '", set the name to "' . $applicationName . "' for one of your software catalog items");
                }
            }

            if (sizeof($availableReleases) > 0) {
                $this->info('  Version differences: ' . sizeof($availableReleases));
            } else {
                $this->info('  ✓ No newer releases available');
            }

            $isFirst = true;

            /** @var Release $helmChartRelease */
            foreach ($availableReleases as $helmChartRelease) {
                $this->info("  - {$helmChartRelease->chartVersion->raw}");

                Release::updateOrCreate([
                    'software_id' => $helmSource->software->id,
                    'version' => $helmChartRelease->chartVersion->raw,
                    'is_latest' => $isFirst,
                ], [
                ]);

                if ($finalSoftwareCandidate && $helmChartRelease->application->version) {
                    $this->info('    - Application version: ' . $helmChartRelease->application->version);
                    $this->info('      Releasing application "' . $finalSoftwareCandidate->name . '" with version"' . $helmChartRelease->application->version . '"');

                    Release::updateOrCreate([
                        'software_id' => $finalSoftwareCandidate->id,
                        'version' => $helmChartRelease->application->version->raw
                    ]);
                }

                $isFirst = false;
            }
        }
    }
}
