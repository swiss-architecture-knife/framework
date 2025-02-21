<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Swark\DataModel\Cloud\Entity\Account;
use Swark\DataModel\Cloud\Entity\AvailabilityZone;
use Swark\DataModel\Cloud\Entity\ManagedBaremetal;
use Swark\DataModel\Cloud\Entity\Offer;
use Swark\DataModel\Cloud\Entity\Region;
use Swark\DataModel\Deployment\Domain\Entity\ApplicationInstance;
use Swark\DataModel\Deployment\Domain\Entity\Deployment;
use Swark\DataModel\Deployment\Domain\Entity\Stage;
use Swark\DataModel\Ecosystem\Domain\Entity\ArchitectureType;
use Swark\DataModel\Ecosystem\Domain\Entity\NamingType;
use Swark\DataModel\Ecosystem\Domain\Entity\Organization;
use Swark\DataModel\Ecosystem\Domain\Entity\ProtocolStack;
use Swark\DataModel\Ecosystem\Domain\Entity\ResourceType;
use Swark\DataModel\Ecosystem\Domain\Entity\Technology;
use Swark\DataModel\Ecosystem\Domain\Entity\TechnologyVersion;
use Swark\DataModel\Ecosystem\Domain\Model\TechnologyType;
use Swark\DataModel\Enterprise\Domain\Entity\Criticality;
use Swark\DataModel\Enterprise\Domain\Entity\System;
use Swark\DataModel\Enterprise\Domain\Entity\Zone;
use Swark\DataModel\Infrastructure\Domain\Entity\Baremetal;
use Swark\DataModel\Infrastructure\Domain\Entity\Cluster;
use Swark\DataModel\Infrastructure\Domain\Entity\Host;
use Swark\DataModel\Infrastructure\Domain\Entity\Resource;
use Swark\DataModel\Infrastructure\Domain\Entity\Runtime;
use Swark\DataModel\Network\Domain\Entity\DnsRecord;
use Swark\DataModel\Network\Domain\Entity\DnsZone;
use Swark\DataModel\Network\Domain\Entity\IpAddress;
use Swark\DataModel\Network\Domain\Entity\IpNetwork;
use Swark\DataModel\Network\Domain\Entity\Nic;
use Swark\DataModel\Network\Domain\Entity\Vlan;
use Swark\DataModel\Software\Domain\Entity\ArtifactType;
use Swark\DataModel\Software\Domain\Entity\Component;
use Swark\DataModel\Software\Domain\Entity\Layer;
use Swark\DataModel\Software\Domain\Entity\Release;
use Swark\DataModel\Software\Domain\Entity\Service;
use Swark\DataModel\Software\Domain\Entity\Software;
use Swark\DataModel\Software\Domain\Entity\Source;
use Swark\DataModel\Software\Domain\Entity\SourceProvider;
use Swark\DataModel\Software\Domain\Model\ReleaseTrain;
use Swark\DataModel\Software\Domain\Model\UsageType;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $vlan = Vlan::updateOrCreate(['number' => '1', 'scomp_id' => 'vlan-default']);
        $ipNetwork = IpNetwork::updateOrCreate([
            'type' => '4',
            'network' => '10.0.0.0',
            'network_mask' => '255.255.0.0',
            'vlan_id' => $vlan->id
        ]);

        $defaultGw = IpAddress::updateOrCreate(['address' => '10.0.0.1', 'ip_network_id' => $ipNetwork->id]);
        $ipNetwork->gateway()->associate($defaultGw->id);
        $ipNetwork->save();

        $dreitierZone = DnsZone::updateOrCreate(['zone' => 'dreitier.com']);


        ArchitectureType::updateOrCreate(['scomp_id' => 'x86'], ['name' => 'x86']);
        ArchitectureType::updateOrCreate(['scomp_id' => 'arm64'], ['name' => 'arm64']);
        ArchitectureType::updateOrCreate(['scomp_id' => 'armhf'], ['name' => 'armhf']);

        ArtifactType::updateOrCreate(['scomp_id' => 'helm'], ['name' => 'Helm']);
        ArtifactType::updateOrCreate(['scomp_id' => 'container_image'], ['name' => 'Container image']);
        ArtifactType::updateOrCreate(['scomp_id' => 'zip'], ['name' => 'ZIP']);
        ArtifactType::updateOrCreate(['scomp_id' => 'exe'], ['name' => 'Executable']);
        ArtifactType::updateOrCreate(['scomp_id' => 'msi'], ['name' => 'MSI']);

        NamingType::updateOrCreate(['scomp_id' => 'internal_jira'], ['name' => 'My private Jira instance', 'public_format' => 'https://my-tenant-id.atlassian.net/items/{0}', 'is_unique_in_type' => true]);
        NamingType::updateOrCreate(['scomp_id' => 'ipv4'], ['name' => 'IPv4', 'public_format' => 'http://{0}', 'is_unique_in_type' => null] /* needs custom unique tester for private IPs */);
        NamingType::updateOrCreate(['scomp_id' => 'ipv6'], ['name' => 'IPv6', 'public_format' => 'http://{0}', 'is_unique_in_type' => null /* needs custom unique tester for private IPs */]);
        NamingType::updateOrCreate(['scomp_id' => 'kubernetes-uid'], ['name' => 'Kubernetes UID', 'is_unique_in_type' => true]);

        $redhat = Organization::updateOrCreate(['name' => 'RedHat'], ['is_vendor' => true]);
        $microsoft = Organization::updateOrCreate(['name' => 'Microsoft'], ['is_vendor' => true]);
        $dreitier = Organization::updateOrCreate(['name' => 'dreitier GmbH'], ['is_vendor' => true, 'is_internal' => true, 'is_managed_service_provider' => true]);

        $wittingen = Region::updateOrCreate(['name' => 'Wittingen Mothership', 'managed_service_provider_id' => $dreitier->id]);
        AvailabilityZone::updateOrCreate(['name' => 'Serverraum', 'region_id' => $wittingen->id]);
        $dreitierInternKst = Account::updateOrCreate(['name' => 'KST-INTERN', 'managed_service_provider_id' => $dreitier->id]);
        $dreitierIntern = Offer::updateOrCreate(['name' => 'Interne Administration', 'managed_service_provider_id' => $dreitier->id]);

        $lumen = Organization::updateOrCreate(['name' => '120 Lumen GmbH'], ['is_customer' => true]);
        $acme = Organization::updateOrCreate(['name' => 'ACME GmbH'], ['is_customer' => true]);
        $atlassian = Organization::updateOrCreate(['name' => 'Atlassian'], ['is_managed_service_provider' => true]);
        $aws = Organization::updateOrCreate(['name' => 'Amazon Web Services'], ['is_managed_service_provider' => true]);
        $ec2 = Offer::updateOrCreate(['name' => 'EC2', 'managed_service_provider_id' => $aws->id]);
        $rds = Offer::updateOrCreate(['name' => 'RDS', 'managed_service_provider_id' => $aws->id]);
        $s3 = Offer::updateOrCreate(['name' => 'S3', 'managed_service_provider_id' => $aws->id]);

        $eucentral1 = Region::updateOrCreate(['name' => 'eu-central-1', 'scomp_id' => 'eu-central-1', 'managed_service_provider_id' => $aws->id]);
        $eucentral1A = AvailabilityZone::updateOrCreate(['name' => 'a', 'scomp_id' => 'a', 'region_id' => $eucentral1->id]);
        $awsTestAccount = Account::updateOrCreate(['name' => '555-555-1111', 'managed_service_provider_id' => $aws->id]);
        $awsProdAccount = Account::updateOrCreate(['name' => '555-555-6666', 'managed_service_provider_id' => $aws->id]);

        $microsoft = Organization::updateOrCreate(['name' => 'Microsoft'], ['is_managed_service_provider' => true, 'is_vendor' => true]);
        $cloudFoundation = Organization::updateOrCreate(['name' => 'CloudFoundation'], ['is_vendor' => true]);
        $broadcom = Organization::updateOrCreate(['name' => 'Broadcom'], ['is_vendor' => true, 'is_managed_service_provider' => true]);
        $oracle = Organization::updateOrCreate(['name' => 'Oracle'], ['is_vendor' => true]);

        $b2b = Zone::updateOrCreate(['name' => 'B2B']);
        $dmz = Zone::updateOrCreate(['name' => 'DMZ']);
        $internal = Zone::updateOrCreate(['name' => 'Internal']);

        $frontend = Layer::updateOrCreate(['name' => 'Frontend']);
        $api = Layer::updateOrCreate(['name' => 'API']);
        $backend = Layer::updateOrCreate(['name' => 'Backend']);
        $persistence = Layer::updateOrCreate(['name' => 'Persistence']);

        $csharp = Technology::updateOrCreate(['name' => 'C#', 'type' => TechnologyType::LANGUAGE]);

        TechnologyVersion::updateOrCreate(['name' => '4.7.2', 'technology_id' => $csharp->id]);
        TechnologyVersion::updateOrCreate(['name' => '9.0', 'technology_id' => $csharp->id]);

        $java = Technology::updateOrCreate(['name' => 'Java', 'type' => TechnologyType::LANGUAGE]);
        $go = Technology::updateOrCreate(['name' => 'Go', 'type' => TechnologyType::LANGUAGE]);

        $ip = Technology::updateOrCreate(['name' => 'IP', 'type' => TechnologyType::PROTOCOL]);
        $tcp = Technology::updateOrCreate(['name' => 'TCP', 'type' => TechnologyType::PROTOCOL]);
        $tls = Technology::updateOrCreate(['name' => 'TLS', 'type' => TechnologyType::PROTOCOL]);
        $http = Technology::updateOrCreate(['name' => 'HTTP', 'type' => TechnologyType::PROTOCOL]);
        $json = Technology::updateOrCreate(['name' => 'JSON', 'type' => TechnologyType::DATA_FORMAT]);

        $restApiCommunication = ProtocolStack::updateOrCreate([
            'name' => 'REST/JSON over HTTPS',
            'application_layer_id' => $json->latest()->id,
            'presentation_layer_id' => $http->latest()->id,
            'session_layer_id' => $tls->latest()->id,
            'transport_layer_id' => $tcp->latest()->id,
            'network_layer_id' => $ip->latest()->id,
        ]);

        $stageTest = Stage::updateOrCreate(['name' => 'TEST']);
        $stageProd = Stage::updateOrCreate(['name' => 'PROD']);

        $lowCriticality = Criticality::updateOrCreate(['name' => 'low'], ['position' => 1]);
        $mediumCriticality = Criticality::updateOrCreate(['name' => 'medium'], ['position' => 2]);
        $highCriticality = Criticality::updateOrCreate(['name' => 'high'], ['position' => 3]);

        $fedora = Software::updateOrCreate(['name' => 'Fedora Linux'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $highCriticality->id,
            'is_operating_system' => true,
            'vendor_id' => $redhat->id,
        ]);
        $fedora2024 = Release::updateOrCreate(['version' => '6.61', 'software_id' => $fedora->id], []);

        $jira = Software::updateOrCreate(['name' => 'Jira'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $mediumCriticality->id,
            'vendor_id' => $atlassian->id
        ]);

        $jiraInTheCloud = Offer::updateOrCreate(['software_id' => $jira->id, 'managed_service_provider_id' => $atlassian->id], ['name' => 'Jira in the Cloud']);

        $githubSourceProvider = SourceProvider::updateOrCreate(['name' => 'GitHub'], [
            'type' => 'github',
            'path' => null,
            'options' => [
                'credentials' => null
            ],
        ]);

        $artifactHubSourceProvider = SourceProvider::updateOrCreate(['name' => 'Artifacthub (default)'], [
            'type' => 'artifacthub'
        ]);

        $loki = Software::updateOrCreate(['name' => 'loki']);

        $lokiChangelog = Source::updateOrCreate([
            'type' => 'changelog',
            'path' => 'grafana/loki',
            'software_id' => $loki->id,
            'source_provider_id' => $githubSourceProvider->id,
            'options' => [
                'tag_prefix' => 'v',
            ]
        ]);

        $lokiHelm = Software::updateOrCreate(['name' => 'loki-helm-chart'], [
            'is_bundle' => true
        ]);

        $lokiHelmSource = Source::updateOrCreate([
            'type' => 'helm',
            'path' => 'grafana/loki',
            'software_id' => $lokiHelm->id,
            'source_provider_id' => $artifactHubSourceProvider->id
        ]);

        $lokiHelmChangelog = Source::updateOrCreate([
            'type' => 'changelog',
            'path' => 'grafana/loki',
            'software_id' => $lokiHelm->id,
            'source_provider_id' => $githubSourceProvider->id,
            'options' => [
                'tag_prefix' => 'v',
            ]
        ]);

        $grafanaHelm = Software::updateOrCreate(['name' => 'grafana-helm-chart'], [
            'is_bundle' => true
        ]);

        $grafanaHelmSource = Source::updateOrCreate([
            'type' => 'helm',
            'path' => 'grafana/grafana',
            'software_id' => $grafanaHelm->id,
            'source_provider_id' => $artifactHubSourceProvider->id,
        ]);

        $keycloakHelm = Software::updateOrCreate(['name' => 'keycloak-helm-chart'], [
            'is_bundle' => true
        ]);

        $keycloakHelmSource = Source::updateOrCreate([
            'type' => 'helm',
            'path' => 'bitnami/keycloak',
            'software_id' => $keycloakHelm->id,
            'source_provider_id' => $artifactHubSourceProvider->id,
        ]);

        $backmon = Software::updateOrCreate(['name' => 'backmon'], [
            'usage_type' => UsageType::CONSOLE,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $mediumCriticality->id,
            'vendor_id' => $dreitier->id
        ]);

        $backmonSource = Source::updateOrCreate([
            'type' => 'code',
            'path' => 'dreitier/backmon',
            'software_id' => $backmon->id,
            'source_provider_id' => $githubSourceProvider->id,
        ]);

        $backmonBackendComponent = Component::updateOrCreate(['software_id' => $backmon->id, 'name' => 'Backend']);
        $backmonApiComponent = Component::updateOrCreate(['software_id' => $backmon->id, 'name' => 'API']);

        $backmonBackendComponent->layers()->sync($backend);
        $backmonApiComponent->layers()->sync($api);
        $backmon->zone()->associate($internal);
        $backmon->save();

        $backmonApiService = Service::updateOrCreate([
            'name' => 'API service',
            'component_id' => $backmonApiComponent->id
        ]);

        $backmonApiService->protocolStacks()->sync($restApiCommunication);
        $backmonApiService->save();

        $gitlab = Software::updateOrCreate(['name' => 'Gitlab'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $lowCriticality->id,
        ]);

        $gitlabRelease = Release::updateOrCreate(['version' => '16.4.1', 'software_id' => $gitlab->id]);

        $postgres = Software::updateOrCreate(['name' => 'PostgreSQL'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $lowCriticality->id,
        ]);

        $postgresRelease = Release::updateOrCreate(['version' => '16.2', 'software_id' => $postgres->id]);

        $oracleDatabase = Software::updateOrCreate(['name' => 'Oracle Database'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $highCriticality->id,
            'vendor_id' => $oracle->id,
        ]);

        $oracle19 = Release::updateOrCreate(['version' => '19.0.0.1', 'software_id' => $oracleDatabase->id]);

        $backmonLatest = Release::updateOrCreate(['version' => '0.2', 'software_id' => $backmon->id]);

        $windows = Software::updateOrCreate(['name' => 'Windows Server'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $highCriticality->id,
            'vendor_id' => $microsoft->id,
            'is_operating_system' => true,
        ]);

        $windows2019 = Release::updateOrCreate(['version' => '2019', 'software_id' => $windows->id], []);

        $exchange = Software::updateOrCreate(['name' => 'Exchange Server'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $highCriticality->id,
            'vendor_id' => $microsoft->id,
        ]);

        $exchange2019 = Release::updateOrCreate(['version' => '2019', 'software_id' => $exchange->id]);

        $hyperV = Software::updateOrCreate(['name' => 'Hyper-V'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $highCriticality->id,
            'vendor_id' => $microsoft->id,
            'is_virtualizer' => true,
        ]);

        $kubernetes = Software::updateOrCreate(['name' => 'Kubernetes'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $highCriticality->id,
            'vendor_id' => $cloudFoundation->id,
            'is_runtime' => true,
        ]);

        $kubernetesRelease = Release::updateOrCreate(['software_id' => $kubernetes->id, 'version' => '1.26.14']);

        $esx = Software::updateOrCreate(['name' => 'ESX vSphere'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $highCriticality->id,
            'vendor_id' => $broadcom->id,
            'is_virtualizer' => true,
            'is_operating_system' => true,
        ]);

        $esxRelease = Release::updateOrCreate(['software_id' => $esx->id, 'version' => '2024.01']);

        $ec2Instance = Baremetal::updateOrCreate(['name' => 'i - 5555a1a6666']);
        $ec2InstanceAssignment = ManagedBaremetal::updateOrCreate(['baremetal_id' => $ec2Instance->id, 'managed_offer_id' => $ec2->id, 'managed_account_id' => $awsProdAccount->id, 'availability_zone_id' => $eucentral1A->id]);

        $srv01 = Baremetal::updateOrCreate(['name' => 'DELL - 01 - VRT']);
        $srv02 = Baremetal::updateOrCreate(['name' => 'DELL - 02']);

        $srv01Host = Host::updateOrCreate(['name' => 'DELL - 01.esx'], [
            'operating_system_id' => $esxRelease->id,
            'virtualizer_id' => $esxRelease->id,
            'baremetal_id' => $srv01->id
        ]);

        $srv01Ex1 = Host::updateOrCreate(['name' => 'EX1'], [
            'operating_system_id' => $windows2019->id,
            'parent_host_id' => $srv01Host->id
        ]);

        $srv01Db1 = Host::updateOrCreate(['name' => 'DB1'], [
            'operating_system_id' => $windows2019->id,
            'parent_host_id' => $srv01Host->id
        ]);

        $devHost = Host::updateOrCreate(['name' => 'DEV'], [
            'operating_system_id' => $fedora2024->id,
            'parent_host_id' => $srv01Host->id
        ]);

        $devNic = Nic::updateOrCreate(['name' => 'eth0', 'mac_address' => 'FF:FF:FF', 'vlan_id' => $vlan->id, 'equipable_type' => 'Host', 'equipable_id' => $devHost->id]);
        $ipForDevNic = IpAddress::updateOrCreate(['ip_network_id' => $ipNetwork->id, 'address' => '10.0.0.1']);
        $devNic->ipAddresses()->attach($ipForDevNic->id);

        $dnsRecord = DnsRecord::updateOrCreate(['name' => 'router', 'dns_zone_id' => $dreitierZone->id, 'ip_address_id' => $devNic->id]);

        $gitlabOnDevHost = ApplicationInstance::updateOrCreate([
            'release_id' => $gitlabRelease->id,
            'executor_id' => $devHost->id,
            'executor_type' => 'host'
        ], []);

        $postgresOnDevHost = ApplicationInstance::updateOrCreate([
            'release_id' => $postgresRelease->id,
            'executor_id' => $devHost->id,
            'executor_type' => 'host'
        ]);

        $srv01Db1Oracle = ApplicationInstance::updateOrCreate([
            'release_id' => $oracle19->id,
            'executor_id' => $srv01Db1->id,
            'executor_type' => 'host',], [
            'stage_id' => $stageProd->id,
        ]);

        $srv01Db2 = Host::updateOrCreate(['name' => 'DB2'], [
            'operating_system_id' => $windows2019->id,
            'parent_host_id' => $srv01Host->id
        ]);

        $srv01Db2Oracle = ApplicationInstance::updateOrCreate(['release_id' => $oracle19->id,
            'executor_id' => $srv01Db2->id,
            'executor_type' => 'host',
        ], [
            'stage_id' => $stageProd->id,
        ]);

        $exchange = ApplicationInstance::updateOrCreate([
            'release_id' => $exchange2019->id,
            'executor_id' => $srv01Ex1->id,
            'executor_type' => 'host'],
            [
                'stage_id' => $stageProd->id
            ]);

        $srv02Fedora = Host::updateOrCreate(['name' => 'k8s-master.node.local'], [
            'baremetal_id' => $srv02->id,
            'operating_system_id' => $fedora2024->id
        ]);

        $k8sRuntime = Runtime::updateOrCreate(['name' => 'k8s - local'], [
            'host_id' => $srv02Fedora->id,
            'release_id' => $kubernetesRelease->id,
        ]);

        $backmonDeployed = ApplicationInstance::updateOrCreate([
            'release_id' => $backmonLatest->id,
            'executor_type' => 'runtime',
            'executor_id' => $k8sRuntime->id
        ]);

        $databaseSchema = ResourceType::updateOrCreate(['name' => 'Database schema', 'scomp_id' => 'db-schema']);
        $messageQueue = ResourceType::updateOrCreate(['name' => 'Message queue', 'scomp_id' => 'message-queue']);

        $oracleCluster = Cluster::updateOrCreate(['name' => 'oracle - prod - cluster']);
        $oracleCluster->applicationInstances()->sync([$srv01Db1Oracle->id, $srv01Db2Oracle->id]);

        $dbProdSchema = Resource::updateOrCreate(['name' => 'sak_schema_prod'], [
            'resource_type_id' => $databaseSchema->id,
            'provider_type' => 'cluster',
            'provider_id' => $oracleCluster->id
        ]);

        $gitlabProdDatabase = Resource::updateOrCreate(['name' => 'gitlab_prod'], [
            'resource_type_id' => $databaseSchema->id,
            'provider_type' => 'application_instance',
            'provider_id' => $postgresOnDevHost->id
        ]);

        $devstackSystem = System::updateOrCreate(['name' => 'Devstack']);
        $devstackRT1_0 = ReleaseTrain::updateOrCreate(['name' => '1.0', 'system_id' => $devstackSystem->id, 'is_latest' => true]);
        $devstackRT1_0->releases()->saveMany([
            $gitlabRelease,
            $postgresRelease
        ]);

        $devstack = Deployment::updateOrCreate(['name' => 'Devstack 1.0 deployment', 'release_train_id' => $devstackRT1_0->id]);
        $devstack->applicationInstances()->saveMany([
            $gitlabOnDevHost,
            $postgresOnDevHost,
        ]);

        $devstack->resources()->saveMany([
            $gitlabProdDatabase,
        ]);
    }
}
