<?php

namespace Swark\Services\Data\Excel\Import;

use Illuminate\Foundation\Application;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Swark\Services\Data\CompositeKeyContainer;
use Swark\Services\Data\Excel\Sheet\ActionsSheet;
use Swark\Services\Data\Excel\Sheet\ActorsSheet;
use Swark\Services\Data\Excel\Sheet\ApplicationInstancesSheet;
use Swark\Services\Data\Excel\Sheet\BaremetalsSheet;
use Swark\Services\Data\Excel\Sheet\ClusterMembersSheet;
use Swark\Services\Data\Excel\Sheet\ClustersSheet;
use Swark\Services\Data\Excel\Sheet\ConnectionsSheet;
use Swark\Services\Data\Excel\Sheet\CriticalitySheet;
use Swark\Services\Data\Excel\Sheet\DataClassificationSheet;
use Swark\Services\Data\Excel\Sheet\FindingsSheet;
use Swark\Services\Data\Excel\Sheet\HostsSheet;
use Swark\Services\Data\Excel\Sheet\LogicalZonesSheet;
use Swark\Services\Data\Excel\Sheet\ManagedAccountsSheet;
use Swark\Services\Data\Excel\Sheet\ManagedOffersSheet;
use Swark\Services\Data\Excel\Sheet\ManagedSubscriptionsSheet;
use Swark\Services\Data\Excel\Sheet\MeasurementPeriodsSheet;
use Swark\Services\Data\Excel\Sheet\MetricsSheet;
use Swark\Services\Data\Excel\Sheet\PoliciesSheet;
use Swark\Services\Data\Excel\Sheet\ProtectionGoalsSheet;
use Swark\Services\Data\Excel\Sheet\ProtocolStacksSheet;
use Swark\Services\Data\Excel\Sheet\RegulationChaptersSheet;
use Swark\Services\Data\Excel\Sheet\RegulationControlsSheet;
use Swark\Services\Data\Excel\Sheet\RegulationsSheet;
use Swark\Services\Data\Excel\Sheet\ReleasesSheet;
use Swark\Services\Data\Excel\Sheet\ResourceTypesSheet;
use Swark\Services\Data\Excel\Sheet\ResourceUsagesSheet;
use Swark\Services\Data\Excel\Sheet\RulesSheet;
use Swark\Services\Data\Excel\Sheet\RuntimesSheet;
use Swark\Services\Data\Excel\Sheet\ScopeTemplatesSheet;
use Swark\Services\Data\Excel\Sheet\SoftwareSheet;
use Swark\Services\Data\Excel\Sheet\StagesSheet;
use Swark\Services\Data\Excel\Sheet\StrategiesSheet;
use Swark\Services\Data\Excel\Sheet\StrategyObjectivesSheet;
use Swark\Services\Data\Excel\Sheet\StrategyQuestionsSheet;
use Swark\Services\Data\Excel\Sheet\SystemsSheet;
use Swark\Services\Data\Excel\Sheet\TechnologySheet;
use Swark\Services\Data\ImportOptions;


class SwarkExcelImport implements WithMultipleSheets
{
    public function __construct(public readonly ImportOptions $options)
    {
        // all sheets require a shared key container
        app()->singleton(CompositeKeyContainer::class, fn(Application $app) => new CompositeKeyContainer());
    }


    private function enableSheetGroup(string $sheetGroupName, array $existingSheets, callable $sheetProvider): array
    {
        if ($this->options->has($sheetGroupName)) {
            $existingSheets = array_merge($existingSheets, $sheetProvider());
        }

        return $existingSheets;
    }

    protected function withDefaultSheets(array $sheets): array
    {
        return $this->enableSheetGroup('default', $sheets, function () {
            return [
                app()->make(TechnologySheet::class),
                app()->make(CriticalitySheet::class),
                app()->make(ProtectionGoalsSheet::class),
                app()->make(DataClassificationSheet::class),
            ];
        });
    }

    protected function withRegulationsSheets(array $sheets): array
    {
        $regulationChaptersSheet = app()->make(RegulationChaptersSheet::class);
        $regulationControlsSheet = app()->make(RegulationControlsSheet::class);

        return $this->enableSheetGroup('regulations', $sheets, function () use ($regulationChaptersSheet, $regulationControlsSheet) {
            return [
                app()->make(RegulationsSheet::class),
                $regulationChaptersSheet,
                $regulationControlsSheet,
            ];
        });
    }

    protected function withRulesSheets(array $sheets): array
    {
        return $this->enableSheetGroup('rules', $sheets, function () {
            return [
                app()->make(ScopeTemplatesSheet::class),
                app()->make(PoliciesSheet::class),
                app()->Make(RulesSheet::class),
            ];
        });
    }

    protected function withStrategiesSheets(array $sheets): array
    {
        return $this->enableSheetGroup('strategies', $sheets, function () {
            return [
                app()->make(StrategiesSheet::class),
                app()->make(StrategyQuestionsSheet::class),
                app()->make(StrategyObjectivesSheet::class),
                app()->make(FindingsSheet::class),
                app()->make(ActionsSheet::class),
                app()->make(MeasurementPeriodsSheet::class),
                app()->make(MetricsSheet::class)
            ];
        });
    }

    protected function withInfrastructureSheets(array $sheets): array
    {
        return $this->enableSheetGroup('infrastructure', $sheets, function () {
            return [
                app()->make(ActorsSheet::class),
                app()->make(LogicalZonesSheet::class),
                app()->make(StagesSheet::class),
                app()->make(ProtocolStacksSheet::class),
                app()->make(ResourceTypesSheet::class),
                app()->make(SoftwareSheet::class),
                app()->make(SystemsSheet::class),
                app()->make(ReleasesSheet::class),
                // cloud
                app()->make(ManagedAccountsSheet::class),
                app()->make(ManagedOffersSheet::class),
                app()->make(ManagedSubscriptionsSheet::class),
                // hardware
                app()->make(BaremetalsSheet::class),
                app()->make(ClustersSheet::class),
                app()->make(HostsSheet::class),
                app()->make(RuntimesSheet::class),

                app()->make(ApplicationInstancesSheet::class),
                app()->make(ClusterMembersSheet::class),
                app()->make(ResourceUsagesSheet::class),
                app()->make(ConnectionsSheet::class),
            ];
        });
    }

    private ?array $sheets = [];

    public function sheets(): array
    {
        // always return the same sheets
        if (null == $this->sheets) {
            $sheets = [];

            $sheets = $this->withDefaultSheets($sheets);
            $sheets = $this->withRegulationsSheets($sheets);
            $sheets = $this->withRulesSheets($sheets);
            $sheets = $this->withStrategiesSheets($sheets);
            $sheets = $this->withInfrastructureSheets($sheets);

            $this->sheets = $sheets;
        }

        return $this->sheets;
    }
}
