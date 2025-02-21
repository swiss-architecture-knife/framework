<?php

namespace Swark\Api\Client;

use Swark\Api\Client\Domain\Baremetal\BaremetalApiClient;
use Swark\Api\Client\Domain\Cluster\ClusterApiClient;
use Swark\Api\Client\Domain\Host\HostApiClient;
use Swark\Api\Client\Domain\Namespace\NamespaceApiClient;
use Swark\Api\Client\Domain\Runtime\RuntimeApiClient;
use Swark\Api\Client\Domain\Software\SoftwareApiClient;

class Swark
{
    private function getDefaults(): array
    {
        return [
            'endpoint' => 'http://localhost:8000/api',
        ];
    }

    private ?Context $context = null;

    private function context(): Context
    {
        if (!$this->context) {
            $this->context = new Context($this->getDefaults());
        }

        return $this->context;
    }

    public function baremetals(): BaremetalApiClient
    {
        return $this->get('baremetal', fn() => new BaremetalApiClient($this->context()));
    }

    public function hosts(): HostApiClient
    {
        return $this->get('hosts', fn() => new HostApiClient($this->context()));
    }

    public function software(): SoftwareApiClient
    {
        return $this->get('software', fn() => new SoftwareApiClient($this->context()));
    }

    public function clusters(): ClusterApiClient
    {
        return $this->get('cluster', fn() => new ClusterApiClient($this->context()));
    }

    public function namespaces(): NamespaceApiClient
    {
        return $this->get('namespace', fn() => new NamespaceApiClient($this->context()));
    }

    public function runtimes(): RuntimeApiClient
    {
        return $this->get('runtime', fn() => new RuntimeApiClient($this->context()));
    }

    private array $cache = [];

    private function get($key, callable $init)
    {
        if (!isset($this->cache[$key])) {
            $this->cache[$key] = $init();
        }

        return $this->cache[$key];
    }
}
