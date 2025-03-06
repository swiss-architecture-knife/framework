<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel;

use Swark\DataModel\Infrastructure\Exchange\CompositeKeyContainer;
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

/**
 * Create a new data-model Excel file with all available sheets in the correct order
 */
class DataModelExcelFileFactory
{
    private array $classes = [
        DataClassificationSheet::class,
        TechnologySheet::class,
        CriticalitySheet::class,
        ProtectionGoalsSheet::class,
        RegulationsSheet::class,
        RegulationChaptersSheet::class,
        RegulationControlsSheet::class,
        ScopeTemplatesSheet::class,
        PoliciesSheet::class,
        RulesSheet::class,
        StrategiesSheet::class,
        StrategyQuestionsSheet::class,
        StrategyObjectivesSheet::class,
        FindingsSheet::class,
        ActionsSheet::class,
        MeasurementPeriodsSheet::class,
        MetricsSheet::class,
        ActorsSheet::class,
        LogicalZonesSheet::class,
        StagesSheet::class,
        ProtocolStacksSheet::class,
        ResourceTypesSheet::class,
        SoftwareSheet::class,
        SystemsSheet::class,
        ReleasesSheet::class,
        ManagedAccountsSheet::class,
        ManagedOffersSheet::class,
        ManagedSubscriptionsSheet::class,
        BaremetalsSheet::class,
        HostsSheet::class,
        RuntimesSheet::class,
        ApplicationInstancesSheet::class,
        ClustersSheet::class,
        ClusterMembersSheet::class,
        HostsBelongingToClusterSheet::class,
        ResourceUsagesVirtualSheet::class,
        ConnectionsVirtualSheet::class,
    ];

    private function createDataModel(array $classes): DataModelExcelFile
    {
        return new DataModelExcelFile(app()->make(CompositeKeyContainer::class), $classes);
    }

    public function createForExport(): DataModelExcelFile
    {
        // the following sheets are "meta" sheets and only relevant for importing data but not exporting
        $args = array_diff($this->classes, [
            ResourceUsagesVirtualSheet::class,
            ClusterMembersSheet::class,
            HostsBelongingToClusterSheet::class,
            ConnectionsVirtualSheet::class,
        ]);

        return $this->createDataModel($args);
    }

    public function createForImport(): DataModelExcelFile
    {
        return $this->createDataModel($this->classes);
    }
}
