<?php

namespace Swark\Services\Data\Excel\Export;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Swark\Services\Data\Excel\Sheet\ActionsSheet;
use Swark\Services\Data\Excel\Sheet\ActorsSheet;
use Swark\Services\Data\Excel\Sheet\ApplicationInstancesSheet;
use Swark\Services\Data\Excel\Sheet\BaremetalsSheet;
use Swark\Services\Data\Excel\Sheet\ClusterMembersSheet;
use Swark\Services\Data\Excel\Sheet\ClustersSheet;
use Swark\Services\Data\Excel\Sheet\CriticalitySheet;
use Swark\Services\Data\Excel\Sheet\FindingsSheet;
use Swark\Services\Data\Excel\Sheet\HostsSheet;
use Swark\Services\Data\Excel\Sheet\LogicalZonesSheet;
use Swark\Services\Data\Excel\Sheet\ManagedAccountsSheet;
use Swark\Services\Data\Excel\Sheet\ManagedOffersSheet;
use Swark\Services\Data\Excel\Sheet\ManagedServicesSheet;
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


class SwarkExport implements WithMultipleSheets
{

    public function sheets(): array
    {
        return [
            /*
            new TechnologySheet(),
            new CriticalitySheet(),
            new ProtectionGoalsSheet(),
            new RegulationsSheet(),
            new RegulationChaptersSheet(),
            new RegulationControlsSheet(),
            new ScopeTemplatesSheet(),
            new PoliciesSheet(),
            new RulesSheet(),
            new StrategiesSheet(),
            new StrategyQuestionsSheet(),
            new StrategyObjectivesSheet(),
            new FindingsSheet(),
            new ActionsSheet(),
            new MeasurementPeriodsSheet(),
            new MetricsSheet(),
            new ActorsSheet(),
            new LogicalZonesSheet(),
            new StagesSheet(),
            new ProtocolStacksSheet(),
            new ResourceTypesSheet(),
            new SoftwareSheet(),
            new SystemsSheet(),
            new ReleasesSheet(),
            new ManagedAccountsSheet(),
            new ManagedOffersSheet(),
            new ManagedSubscriptionsSheet(),
            new BaremetalsSheet(),
            new ClustersSheet(),
            new HostsSheet(),
            new RuntimesSheet(),
            new ApplicationInstancesSheet(),
            */
            // new need for ResourceUsagesSheet() or ConnectionsSheet(), already exported by ApplicationInstancesSheet
            // no need for ClusterMembersSheet, already exported by ClustersSheet
        ];
    }
}
