<?php
declare(strict_types=1);

namespace Swark\Console\Commands;


use Dreitier\Alm\Inspecting\Kubernetes\ClientContext;
use Dreitier\Alm\Inspecting\Kubernetes\ClientContextFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Swark\Api\Client\Aspects\WithNamedType;
use Swark\Api\Client\Domain\Baremetal\Baremetal;
use Swark\Api\Client\Domain\Baremetal\Placement;
use Swark\Api\Client\Domain\Cluster\Cluster;
use Swark\Api\Client\Domain\Host\Host;
use Swark\Api\Client\Domain\JsonDataResponse;
use Swark\Api\Client\Domain\Runtime\Runtime;
use Swark\Api\Client\Domain\Software\SoftwareResponse;
use Swark\Api\Client\Swark;
use Swark\Api\Client\Types\Id;
use Swark\Api\Client\Types\Name;
use Swark\Api\Client\Types\NamedId;
use Swark\Console\Commands\Support\Cache;

class ImportKubernetesCluster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cluster:import {--kube-config=} {--kube-user=} {--kube-cluster=} {--kube-context=} {--account=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports a Kubernetes cluster';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $swark = new Swark();
        $factory = new ClientContextFactory();
        $context = $factory->createFromKubeConfig(
            kubeConfigPath: $this->option('kube-config'),
            user: $this->option('kube-user'),
            cluster: $this->option('kube-cluster'),
            context: $this->option('kube-context')
        );

        $softwareCache
            = $this->buildSoftwareCache($swark, $context);

        // find all available endpoints from Kubernetes API
        $apiGroups = $context->client->api_groups()->find();

        // k8s API does not allow to find out the name of the current or other clusters
        $clusters = [];

        // with Rancher installed we can use that endpoint to find our own and all downstream clusters
        if ($apiGroups->hasRancherEndpoints()) {
            $clusters = $context->client->rancher_clusters()->find();
        }

        $cluster = new Cluster(
            name: Name::of($context->clusterName),
            id: Id::of($context->clusterName),
        );

        $this->info("Importing cluster " . $context->clusterName . "...");

        $r = $swark->clusters()->upsert($cluster);
        $status = $this->toStatusChanged($r->response);
        $this->info("+ {$status} cluster item {$r->name}");

        $this->importNodes($softwareCache, $context, $swark);
    }

    private function importNodes(Cache $softwareCache, ClientContext $context, Swark $swark)
    {
        $client = $context->client;
        $nodes = $client->nodes()->find();

        foreach ($nodes as $node) {
            $node = $node->toArray();
            $labels = $node['metadata']['labels'] ?? [];
            $nameIp = $node['metadata']['name'];
            $uid = $node['metadata']['uid'];

            $nodeInfo = $node['status']['nodeInfo'];
            $nodeName = $nodeInfo['machineID'];

            $operatingSystemName = $labels['kubernetes.io/os'];
            $kubeletVersion = $nodeInfo['kubeletVersion'];

            if (empty($nodeName)) {
                $nodeName = $labels['kubernetes.io/hostname'];
            }

            if (!$nodeName) {
                $this->error("Unable to add host $uid / $nameIp: No valid hostname");
                continue;
            }

            $region = null;
            $zone = null;
            $refPlacement = null;
            $baremetal = (new Baremetal(
                name: new Name($nodeName)
            ))
                ->hasName('kubernetes-uid', $uid)
                ->hasName('ipv4', $nameIp);

            if (!empty($labels["topology.kubernetes.io/region"]) && !empty($labels["topology.kubernetes.io/zone"])) {
                $region = $labels["topology.kubernetes.io/region"];
                $zone = Str::replace($labels["topology.kubernetes.io/region"], '', $labels["topology.kubernetes.io/zone"]);

                if (!$account = $this->option('account')) {
                    $this->warn("Zone '$zone' and region '$region' is set. Please provide --account parameter to assign to the correct provider");
                } else {
                    $refPlacement = new Placement(NamedId::of($region), NamedId::of($zone), NamedId::of($account));
                    $baremetal->placement($refPlacement);
                }
            }

            $this->info("Retrieved node $nodeName from cluster...");

            try {
                $r = $swark->baremetals()->upsert(
                    $baremetal
                );

                $status = $this->toStatusChanged($r->response);

                $this->info("+ {$status} node $nodeName ($nameIp)");

                $r = $swark->hosts()->upsert(
                    new Host(name: Name::of($nodeName),
                        operatingSystem: NamedId::of($softwareCache->get($operatingSystemName)),
                        baremetal: NamedId::of($uid, 'kubernetes-uid')
                    )
                        ->hasName('kubernetes-uid', $uid)
                        ->hasName('ipv4', $nameIp)
                    );

                $status = $this->toStatusChanged($r->response);

                $this->info("+    {$status} downstream host $nodeName ($nameIp)");

                // TODO This does not work. We have to store the name AND optionally the version of a software
                // see onMiss()
                $r = $swark->runtimes()->upsert(
                    new Runtime(
                        name: Name::of('node-' . $nodeName . '-k8s'),
                        host: NamedId::of($r->name->value),
                        release: NamedId::of($softwareCache->get('kubernetes:' . $kubeletVersion, ['is_runtime' => true])),
                        id: Id::of('node-' . $nodeName . '-k8s'),
                    )
                );

                $status = $this->toStatusChanged($r->response);

                $this->info("      {$status} runtime {$kubeletVersion} set for host $nodeName");

            } catch (\Illuminate\Http\Client\RequestException $e) {
                $this->error("Unable to do import: " . $e->getMessage());
                $this->error($e->response->body());
            }
        }
    }

    private function toStatusChanged(JsonDataResponse $response)
    {
        return match (true) {
            $response->hasBeenChanged() => 'Upserted',
            $response->hasBeenCreated() => 'Created new',
            $response->noModification() => 'No modifications for',
            default => 'Unknown',
        };
    }

    private function buildSoftwareCache(Swark $swark, ClientContext $context): Cache
    {
        $operatingSystemCache = new Cache();
        $operatingSystemCache->initialize(function () use ($swark) {
            $r = [];

            /** @var SoftwareResponse $software */
            foreach ($swark->software()->find()->items() as $software) {
                $r[$software->name->value] = $software->id . ":" . ($software->favoriteRelease()?->name ?? '*');
            }

            return $r;
        });

        // TODO This does not work. We have to store the name AND optionally the version of a software
        /*$operatingSystemCache->onMiss(function (string $key, array $args = []) use ($swark) {

            //$r = $swark->software()->upsert($software);

            //return $software->id . ":" . '*';
        });
        */

        return $operatingSystemCache;
    }
}

class Name2
{
    public function __construct(public readonly string $value)
    {
    }

    public function toScompId(?string $type = null): ScompId
    {
        return ScompId::of('');
    }

    public function __toString()
    {
        return $this->value;
    }
}

class ScompId
{
    private function __construct(
        public readonly string  $id,
        public readonly string  $source,
        public readonly ?string $type = null,
    )
    {
    }

    public static function of(string $scompifiable): ScompId
    {
        return new static(Str::snake(Str::lower($scompifiable)), $scompifiable);
    }
}

class SoftwareCatalogItem
{
    use WithNamedType;

    public function __construct(
        public readonly string $name,
        public readonly ScompId    $id,
        public readonly array $args = [],
    )
    {
    }

    public function withRelease(string $version, $args = [])
    {
        $this->release = ReleaseReference::of($version, $args);
        return $this;
    }

    public function toCompositeKey(): string
    {
        return '';
    }

    private ?ReleaseReference $release = null;

    public static function of(string $name, $args = [])
    {
        $args['name'] = $name;
        $args['id'] = isset($args['id']) ? ScompId::of($args['id']) : null;
        return new static($name, $args['id'], $args);
    }

    public function toArray(): array
    {
        return [
                'name' => $this->name->value,
                'id' => $this->id?->value,
                'is_runtime' => $this->isRuntime,
                'is_operating_system' => $this->isOperatingSystem,
                '_release' => $this->release?->toArray(),
            ] + $this->joinNamings();
    }
}

class ReleaseReference
{
    public function __construct(
        public readonly Name $version,
        public readonly ?bool                    $isLatest = null,
        public readonly ?bool                    $isAny = null,
    )
    {
    }

    public static function of(string $name, $args = [])
    {
        $args['name'] = $name;
        return new static(... $args);
    }

    public function toArray(): array
    {
        return [
            'version' => $this->version->value,
            'is_latest' => $this->isLatest,
            'is_any' => $this->isAny,
        ];
    }
}
