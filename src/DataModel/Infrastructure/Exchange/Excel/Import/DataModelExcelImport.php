<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Import;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Swark\DataModel\Infrastructure\Exchange\Excel\DataModelExcelFile;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Auditing\ActionsSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Auditing\FindingsSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Auditing\PoliciesSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Auditing\RulesSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Auditing\ScopeTemplatesSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Business\ActorsSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Compliance\DataClassificationSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Compliance\ProtectionGoalsSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Compliance\RegulationChaptersSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Compliance\RegulationControlsSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Compliance\RegulationsSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Governance\CriticalitySheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Governance\Kpi\MeasurementPeriodsSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Governance\Kpi\MetricsSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Governance\StrategiesSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Governance\StrategyObjectivesSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Governance\StrategyQuestionsSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Governance\TechnologySheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Cloud\ManagedAccountsSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Cloud\ManagedOffersSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Cloud\ManagedSubscriptionsSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Component\ApplicationInstancesSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Component\BaremetalsSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Component\ClusterMembersSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Component\ClustersSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Component\HostsBelongingToClusterSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Component\HostsSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Component\RuntimesSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\Component\SystemsSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\ConnectionsVirtualSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\LogicalZonesSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\ProtocolStacksSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\InformationTechnology\ResourceUsagesVirtualSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Meta\ResourceTypesSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Operations\StagesSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\SoftwareArchitecture\ReleasesSheet;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\SoftwareArchitecture\SoftwareSheet;


class DataModelExcelImport implements WithMultipleSheets
{
    public function __construct(
        public readonly DataModelExcelFile     $dataModelExcelFile,
        public readonly DataModelImportOptions $options)
    {
    }


    private function enableSheetGroup(string $sheetGroupName, array $existingSheets, callable $sheetProvider): array
    {
        if ($this->options->isCategoryEnabled($sheetGroupName)) {
            $existingSheets = array_merge($existingSheets, $sheetProvider());
        }

        return $existingSheets;
    }

    protected function withDefaultSheets(array $sheets): array
    {
        return $this->enableSheetGroup('default', $sheets, function () {
            return [
                $this->dataModelExcelFile->resolve(TechnologySheet::class),
                $this->dataModelExcelFile->resolve(CriticalitySheet::class),
                $this->dataModelExcelFile->resolve(ProtectionGoalsSheet::class),
                $this->dataModelExcelFile->resolve(DataClassificationSheet::class),
            ];
        });
    }

    protected function withComplianceSheets(array $sheets): array
    {
        return $this->enableSheetGroup(DataModelImportOptions::COMPLIANCE, $sheets, function () {
            return [
                $this->dataModelExcelFile->resolve(RegulationsSheet::class),
                $this->dataModelExcelFile->resolve(RegulationChaptersSheet::class),
                $this->dataModelExcelFile->resolve(RegulationControlsSheet::class),
            ];
        });
    }

    protected function withAuditingSheets(array $sheets): array
    {
        return $this->enableSheetGroup(DataModelImportOptions::AUDITING, $sheets, function () {
            return [
                $this->dataModelExcelFile->resolve(ScopeTemplatesSheet::class),
                $this->dataModelExcelFile->resolve(PoliciesSheet::class),
                $this->dataModelExcelFile->resolve(RulesSheet::class),
            ];
        });
    }

    protected function withGovernanceSheets(array $sheets): array
    {
        return $this->enableSheetGroup(DataModelImportOptions::GOVERNANCE, $sheets, function () {
            return [
                $this->dataModelExcelFile->resolve(StrategiesSheet::class),
                $this->dataModelExcelFile->resolve(StrategyQuestionsSheet::class),
                $this->dataModelExcelFile->resolve(StrategyObjectivesSheet::class),
                $this->dataModelExcelFile->resolve(MeasurementPeriodsSheet::class),
            ];
        });
    }

    protected function withInfrastructureSheets(array $sheets): array
    {
        return $this->enableSheetGroup(DataModelImportOptions::INFRASTRUCTURE, $sheets, function () {
            return [
                $this->dataModelExcelFile->resolve(ActorsSheet::class),
                $this->dataModelExcelFile->resolve(LogicalZonesSheet::class),
                $this->dataModelExcelFile->resolve(StagesSheet::class),
                $this->dataModelExcelFile->resolve(ProtocolStacksSheet::class),
                $this->dataModelExcelFile->resolve(ResourceTypesSheet::class),
                $this->dataModelExcelFile->resolve(SoftwareSheet::class),
                $this->dataModelExcelFile->resolve(SystemsSheet::class),
                $this->dataModelExcelFile->resolve(ReleasesSheet::class),
                // cloud
                $this->dataModelExcelFile->resolve(ManagedAccountsSheet::class),
                $this->dataModelExcelFile->resolve(ManagedOffersSheet::class),
                $this->dataModelExcelFile->resolve(ManagedSubscriptionsSheet::class),

                // hardware
                $this->dataModelExcelFile->resolve(BaremetalsSheet::class),
                $this->dataModelExcelFile->resolve(ClustersSheet::class),
                $this->dataModelExcelFile->resolve(HostsSheet::class),
                $this->dataModelExcelFile->resolve(RuntimesSheet::class),
                $this->dataModelExcelFile->resolve(ApplicationInstancesSheet::class),
                $this->dataModelExcelFile->resolve(ClusterMembersSheet::class),
                $this->dataModelExcelFile->resolve(HostsBelongingToClusterSheet::class),
                $this->dataModelExcelFile->resolve(ResourceUsagesVirtualSheet::class),
                $this->dataModelExcelFile->resolve(ConnectionsVirtualSheet::class),
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
            $sheets = $this->withComplianceSheets($sheets);
            $sheets = $this->withAuditingSheets($sheets);
            $sheets = $this->withGovernanceSheets($sheets);
            $sheets = $this->withInfrastructureSheets($sheets);

            if ($this->options->isCategoryEnabled(DataModelImportOptions::GOVERNANCE)
                && $this->options->isCategoryEnabled(DataModelImportOptions::AUDITING)) {
                if ($this->options->isCategoryEnabled(DataModelImportOptions::COMPLIANCE)) {
                    $sheets[] = $this->dataModelExcelFile->resolve(FindingsSheet::class);
                    $sheets[] = $this->dataModelExcelFile->resolve(ActionsSheet::class);
                }

                $sheets[] = $this->dataModelExcelFile->resolve(MetricsSheet::class);
            }

            $this->sheets = $sheets;
        }

        return $this->sheets;
    }
}
