<?php

namespace Swark\Services\Data\Excel\Sheet;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Deployment\Domain\Entity\ApplicationInstance;
use Swark\DataModel\Infrastructure\Domain\Entity\ClusterMember;
use Swark\DataModel\Infrastructure\Domain\Entity\Namespace_;
use Swark\DataModel\ModelTypes;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

/**
 * This is marked with HasReferencesToOtherSheets so that database connections and resource usages can be imported later
 */
class ApplicationInstancesSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle, SkipsEmptyRows, HasReferencesToOtherSheets
{
    use Exportable;

    const SOFTWARE_SCOMP_ID_COLUMN = 'software-scomp-id';

    const LOGICAL_ZONE_SCOMP_ID_COLUMN = 'logical-zone-scomp-id';

    const STAGE_SCOMP_ID_COLUMN = 'stage-scomp-id';

    const SYSTEM_SCOMP_ID_COLUMN = 'system-scomp-id';

    const RESOURCE_USAGE_SCOMP_ID_COLUMN = 'resource-usage-scomp-id';
    const CONNECTS_TO_SCOMP_ID_COLUMN = 'connects-to-scomp-id';
    const EXECUTOR_HOST_SCOMP_ID = 'executor-host-scomp-id';
    const EXECUTOR_RUNTIME_SCOMP_ID = 'executor-runtime-scomp-id';
    const HA_REPLICA_OF_SCOMP_ID = 'replica-of-scomp-id';
    const HA_FAILOVER_SCOMP_ID = 'failover-scomp-id';
    const HA_CLUSTER_VIP_SCOMP_ID = 'cluster-vip-scomp-id';
    const KUBERNETES_CLUSTER_SCOMP_ID = 'kubernetes-cluster-scomp-id';
    const KUBERNETES_DEPLOYMENT_SCOMP_ID = 'kubernetes-deployment-scomp-id';
    const KUBERNETES_NAMESPACE_SCOMP_ID = 'kubernetes-namespace-scomp-id';

    public function generator(): \Generator
    {
        yield ['software-scomp-id', 'scomp-id/name', 'logical-zone-scomp-id', 'stage-scomp-id', 'system-scomp-id', 'resource-usage-scomp-id', 'connects-to-scomp-id', 'executor-host-scomp-id', 'executor-runtime-scomp-id', 'ha-replica', 'ha-failover', 'ha-cluster-vip', 'kubernetes-cluster', 'kubernetes-deployment', 'kubernetes-namespace'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Software', static::SOFTWARE_SCOMP_ID_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Logical zone', static::LOGICAL_ZONE_SCOMP_ID_COLUMN))
            ->add(Column::of('Stage', static::STAGE_SCOMP_ID_COLUMN))
            ->add(Column::of('System', static::SYSTEM_SCOMP_ID_COLUMN))
            ->add(Column::of('Database schema', static::RESOURCE_USAGE_SCOMP_ID_COLUMN))
            ->add(Column::of('Connects to', static::CONNECTS_TO_SCOMP_ID_COLUMN))
            ->add(Column::of('Executor')->span(2))
            ->add(Column::of('')->span(3))
            ->add(Column::of('')->span(3))
            ->next()
            ->add(Column::of('${software.scomp_id}'))
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('Host', static::EXECUTOR_HOST_SCOMP_ID))
            ->add(Column::of('Runtime', static::EXECUTOR_RUNTIME_SCOMP_ID))
            ->add(Column::of('HA')->span(3))
            ->add(Column::of('Kubernetes')->span(3))
            ->next()
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('${system.scomp_id}'))
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('${host.scomp_id}'))
            ->add(Column::of('${runtime.scomp_id}'))
            ->add(Column::of('Replica', static::HA_REPLICA_OF_SCOMP_ID))
            ->add(Column::of('Failover', static::HA_FAILOVER_SCOMP_ID))
            ->add(Column::of('Cluster/VIP', static::HA_CLUSTER_VIP_SCOMP_ID))
            ->add(Column::of('Cluster', static::KUBERNETES_CLUSTER_SCOMP_ID))
            ->add(Column::of('Deployment', static::KUBERNETES_DEPLOYMENT_SCOMP_ID))
            ->add(Column::of('Namespace', static::KUBERNETES_NAMESPACE_SCOMP_ID));
    }


    public function title(): string
    {
        return "Application instances";
    }

    protected function importRow(RowContext $row)
    {
        $zoneId = $this->compositeKeyContainer->idOrNull('logical_zone', Str::lower($row[static::LOGICAL_ZONE_SCOMP_ID_COLUMN]));

        $executorId = null;
        $executorType = null;
        $executorScompId = null;

        $executorHost = $row[static::EXECUTOR_HOST_SCOMP_ID];
        $executorRuntime = $row[static::EXECUTOR_RUNTIME_SCOMP_ID];
        $kubernetesCluster = $row[static::KUBERNETES_CLUSTER_SCOMP_ID];
        $kubernetesNamespace = $row[static::KUBERNETES_NAMESPACE_SCOMP_ID];

        if (!empty($executorHost)) {
            $executorType = 'host';
            $executorScompId = $executorHost;
        } elseif (!empty($executorRuntime)) {
            $executorType = 'runtime';
            $executorScompId = $executorRuntime;
        } elseif (!empty($kubernetesCluster) && !empty($kubernetesNamespace)) {
            $executorType = 'cluster';
            $executorScompId = $kubernetesCluster;
        } else {
            throw new \Exception("Can not locate executor type for application instance for row " . print_r($row, true));
        }

        $executorId = $this->compositeKeyContainer->get($executorType, $executorScompId);

        $applicationInstance = ApplicationInstance::updateOrCreate([
            'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
            'release_id' => $this->compositeKeyContainer->get('release', $row[static::SOFTWARE_SCOMP_ID_COLUMN] . ":latest"),
            'executor_type' => $executorType,
            'executor_id' => $executorId,
            'stage_id' => $row->ifPresent(static::STAGE_SCOMP_ID_COLUMN, fn($value) => $this->compositeKeyContainer->get('stage', $value)),
            'logical_zone_id' => $zoneId,
        ], [
            'system_id' => $row->ifPresent(static::SYSTEM_SCOMP_ID_COLUMN, fn($value) => $this->compositeKeyContainer->get('system', $value)),
        ]);

        $this->compositeKeyContainer->set('application_instance', $applicationInstance->scomp_id, $applicationInstance->id);

        if ($executorType == 'cluster') {
            $namespace = Namespace_::updateOrCreate([
                'name' => $kubernetesNamespace,
                'cluster_id' => $executorId
            ]);

            $this->compositeKeyContainer->set('namespace', $kubernetesCluster . ':' . $namespace->name, $namespace->id);

            ClusterMember::updateOrCreate([
                'cluster_id' => $this->compositeKeyContainer->get('cluster', $kubernetesCluster),
                'member_type' => ModelTypes::ApplicationInstance,
                'member_id' => $applicationInstance->id,
                'namespace_id' => $namespace->id,
            ]);
        }
    }
}

