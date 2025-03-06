<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Swark\DataModel\Domain\Model\Governance\TechnologyType;
use Swark\DataModel\Domain\Model\SoftwareArchitecture\ReleaseTrain;
use Swark\DataModel\Domain\Model\SoftwareArchitecture\UsageType;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Organization;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Criticality;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Technology;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\TechnologyVersion;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\ArchitectureType;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Account;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\AvailabilityZone;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\ManagedBaremetal;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Offer;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Region;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Baremetal;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Cluster;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Host;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Resource;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Runtime;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\System;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\DnsRecord;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\DnsZone;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\IpAddress;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\IpNetwork;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\Nic;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\Vlan;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\ProtocolStack;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Zone;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\NamingType;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\ResourceType;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\ApplicationInstance;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\Deployment;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\Stage;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\ArtifactType;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Component;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Layer;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Release;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Service;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Software;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Source;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\SourceProvider;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $vlan = Vlan::upsert('vlan-default', ['name' => 'default', 'number' => '1']);

        $ipNetwork = IpNetwork::updateOrCreate([
            'type' => '4',
            'vlan_id' => $vlan->id,
            'network' => '10.0.0.0',
            'network_mask' => '255.255.0.0',
        ]);

        $defaultGw = IpAddress::updateOrCreate(['address' => '10.0.0.1', 'ip_network_id' => $ipNetwork->id]);
        $ipNetwork->gateway()->associate($defaultGw->id);
        $ipNetwork->save();

        $dreitierZone = DnsZone::updateOrCreate(['zone' => 'dreitier.com']);

        ArchitectureType::upsert(['name' => 'x86']);
        ArchitectureType::upsert(['name' => 'arm64']);
        ArchitectureType::upsert(['name' => 'armhf']);

        ArtifactType::upsert('helm', ['name' => 'Helm']);
        ArtifactType::upsert('container_image', ['name' => 'Container image']);
        ArtifactType::upsert('zip', ['name' => 'ZIP']);
        ArtifactType::upsert('exe', ['name' => 'Executable']);
        ArtifactType::upsert('msi', ['name' => 'MSI']);

        NamingType::updateOrCreate(['scomp_id' => 'internal_jira'], ['name' => 'My private Jira instance', 'public_format' => 'https://my-tenant-id.atlassian.net/items/{0}', 'is_unique_in_type' => true]);
        NamingType::updateOrCreate(['scomp_id' => 'ipv4'], ['name' => 'IPv4', 'public_format' => 'http://{0}', 'is_unique_in_type' => null] /* needs custom unique tester for private IPs */);
        NamingType::updateOrCreate(['scomp_id' => 'ipv6'], ['name' => 'IPv6', 'public_format' => 'http://{0}', 'is_unique_in_type' => null /* needs custom unique tester for private IPs */]);
        NamingType::updateOrCreate(['scomp_id' => 'kubernetes-uid'], ['name' => 'Kubernetes UID', 'is_unique_in_type' => true]);

        $redhat = Organization::upsert(['name' => 'RedHat'], ['is_vendor' => true]);
        $microsoft = Organization::upsert(['name' => 'Microsoft'], ['is_vendor' => true]);
        $dreitier = Organization::upsert(['name' => 'dreitier GmbH'], ['is_vendor' => true, 'is_internal' => true, 'is_managed_service_provider' => true]);

        $wittingen = Region::upsert(['name' => 'Wittingen Mothership', 'managed_service_provider_id' => $dreitier->id]);
        AvailabilityZone::upsert(['name' => 'Serverraum', 'region_id' => $wittingen->id]);
        $dreitierInternKst = Account::upsert(['name' => 'KST-INTERN', 'managed_service_provider_id' => $dreitier->id]);
        $dreitierIntern = Offer::upsert(['name' => 'Interne Administration', 'managed_service_provider_id' => $dreitier->id]);

        $lumen = Organization::upsert(['name' => '120 Lumen GmbH'], ['is_customer' => true]);
        $acme = Organization::upsert(['name' => 'ACME GmbH'], ['is_customer' => true]);
        $atlassian = Organization::upsert(['name' => 'Atlassian'], ['is_managed_service_provider' => true]);
        $aws = Organization::upsert(['name' => 'Amazon Web Services'], ['is_managed_service_provider' => true]);
        $ec2 = Offer::upsert(['name' => 'EC2', 'managed_service_provider_id' => $aws->id]);
        $rds = Offer::upsert(['name' => 'RDS', 'managed_service_provider_id' => $aws->id]);
        $s3 = Offer::upsert(['name' => 'S3', 'managed_service_provider_id' => $aws->id]);

        $eucentral1 = Region::upsert(['name' => 'eu-central-1', 'managed_service_provider_id' => $aws->id]);
        $eucentral1A = AvailabilityZone::upsert(['name' => 'a', 'region_id' => $eucentral1->id]);
        $awsTestAccount = Account::upsert(['name' => '555-555-1111', 'managed_service_provider_id' => $aws->id]);
        $awsProdAccount = Account::upsert(['name' => '555-555-6666', 'managed_service_provider_id' => $aws->id]);

        $microsoft = Organization::upsert(['name' => 'Microsoft'], ['is_managed_service_provider' => true, 'is_vendor' => true]);
        $cloudFoundation = Organization::upsert(['name' => 'CloudFoundation'], ['is_vendor' => true]);
        $broadcom = Organization::upsert(['name' => 'Broadcom'], ['is_vendor' => true, 'is_managed_service_provider' => true]);
        $oracle = Organization::upsert(['name' => 'Oracle'], ['is_vendor' => true]);

        $b2b = Zone::upsert(['name' => 'B2B']);
        $dmz = Zone::upsert(['name' => 'DMZ']);
        $internal = Zone::upsert(['name' => 'Internal']);

        $frontend = Layer::upsert(['name' => 'Frontend']);
        $api = Layer::upsert(['name' => 'API']);
        $backend = Layer::upsert(['name' => 'Backend']);
        $persistence = Layer::upsert(['name' => 'Persistence']);

        $csharp = Technology::upsert(['name' => 'C#', 'type' => TechnologyType::LANGUAGE]);

        TechnologyVersion::upsert(['name' => '4.7.2', 'technology_id' => $csharp->id]);
        TechnologyVersion::upsert(['name' => '9.0', 'technology_id' => $csharp->id]);

        $java = Technology::upsert(['name' => 'Java', 'type' => TechnologyType::LANGUAGE]);
        $go = Technology::upsert(['name' => 'Go', 'type' => TechnologyType::LANGUAGE]);

        $ip = Technology::upsert(['name' => 'IP', 'type' => TechnologyType::PROTOCOL]);
        $tcp = Technology::upsert(['name' => 'TCP', 'type' => TechnologyType::PROTOCOL]);
        $tls = Technology::upsert(['name' => 'TLS', 'type' => TechnologyType::PROTOCOL]);
        $http = Technology::upsert(['name' => 'HTTP', 'type' => TechnologyType::PROTOCOL]);
        $json = Technology::upsert(['name' => 'JSON', 'type' => TechnologyType::DATA_FORMAT]);

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

        $lowCriticality = Criticality::upsert(['name' => 'low'], ['position' => 1]);
        $mediumCriticality = Criticality::upsert(['name' => 'medium'], ['position' => 2]);
        $highCriticality = Criticality::upsert(['name' => 'high'], ['position' => 3]);

        $fedora = Software::upsert(['name' => 'Fedora Linux'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $highCriticality->id,
            'is_operating_system' => true,
            'vendor_id' => $redhat->id,
        ]);
        $fedora2024 = Release::updateOrCreate(['version' => '6.61', 'software_id' => $fedora->id], []);

        $jira = Software::upsert(['name' => 'Jira'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $mediumCriticality->id,
            'vendor_id' => $atlassian->id
        ]);

        $jiraInTheCloud = Offer::upsert(['software_id' => $jira->id, 'managed_service_provider_id' => $atlassian->id, 'name' => 'Jira in the Cloud']);

        $githubSourceProvider = SourceProvider::upsert(['name' => 'GitHub'], [
            'type' => 'github',
            'path' => null,
            'options' => [
                'credentials' => null
            ],
        ]);

        $artifactHubSourceProvider = SourceProvider::upsert(['name' => 'Artifacthub (default)'], [
            'type' => 'artifacthub'
        ]);

        $loki = Software::upsert(['name' => 'loki']);

        $lokiChangelog = Source::updateOrCreate([
            'type' => 'changelog',
            'path' => 'grafana/loki',
            'software_id' => $loki->id,
            'source_provider_id' => $githubSourceProvider->id,
            'options' => [
                'tag_prefix' => 'v',
            ]
        ]);

        $lokiHelm = Software::upsert(['name' => 'loki-helm-chart'], [
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

        $grafanaHelm = Software::upsert(['name' => 'grafana-helm-chart'], [
            'is_bundle' => true
        ]);

        $grafanaHelmSource = Source::updateOrCreate([
            'type' => 'helm',
            'path' => 'grafana/grafana',
            'software_id' => $grafanaHelm->id,
            'source_provider_id' => $artifactHubSourceProvider->id,
        ]);

        $keycloakHelm = Software::upsert(['name' => 'keycloak-helm-chart'], [
            'is_bundle' => true
        ]);

        $keycloakHelmSource = Source::updateOrCreate([
            'type' => 'helm',
            'path' => 'bitnami/keycloak',
            'software_id' => $keycloakHelm->id,
            'source_provider_id' => $artifactHubSourceProvider->id,
        ]);

        $backmon = Software::upsert(['name' => 'backmon'], [
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

        $backmonApiService = Service::upsert([
            'name' => 'API service',
            'component_id' => $backmonApiComponent->id
        ]);

        $backmonApiService->protocolStacks()->sync($restApiCommunication);
        $backmonApiService->save();

        $gitlab = Software::upsert(['name' => 'Gitlab'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $lowCriticality->id,
        ]);

        $gitlabRelease = Release::updateOrCreate(['version' => '16.4.1', 'software_id' => $gitlab->id]);

        $postgres = Software::upsert(['name' => 'PostgreSQL'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $lowCriticality->id,
        ]);

        $postgresRelease = Release::updateOrCreate(['version' => '16.2', 'software_id' => $postgres->id]);

        $oracleDatabase = Software::upsert(['name' => 'Oracle Database'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $highCriticality->id,
            'vendor_id' => $oracle->id,
        ]);

        $oracle19 = Release::updateOrCreate(['version' => '19.0.0.1', 'software_id' => $oracleDatabase->id]);

        $backmonLatest = Release::updateOrCreate(['version' => '0.2', 'software_id' => $backmon->id]);

        $windows = Software::upsert(['name' => 'Windows Server'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $highCriticality->id,
            'vendor_id' => $microsoft->id,
            'is_operating_system' => true,
        ]);

        $windows2019 = Release::updateOrCreate(['version' => '2019', 'software_id' => $windows->id], []);

        $exchange = Software::upsert(['name' => 'Exchange Server'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $highCriticality->id,
            'vendor_id' => $microsoft->id,
        ]);

        $exchange2019 = Release::updateOrCreate(['version' => '2019', 'software_id' => $exchange->id]);

        $hyperV = Software::upsert(['name' => 'Hyper-V'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $highCriticality->id,
            'vendor_id' => $microsoft->id,
            'is_virtualizer' => true,
        ]);

        $kubernetes = Software::upsert(['name' => 'Kubernetes'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $highCriticality->id,
            'vendor_id' => $cloudFoundation->id,
            'is_runtime' => true,
        ]);

        $kubernetesRelease = Release::updateOrCreate(['software_id' => $kubernetes->id, 'version' => '1.26.14']);

        $esx = Software::upsert(['name' => 'ESX vSphere'], [
            'usage_type' => UsageType::SERVER,
            'business_criticality_id' => $lowCriticality->id,
            'infrastructure_criticality_id' => $highCriticality->id,
            'vendor_id' => $broadcom->id,
            'is_virtualizer' => true,
            'is_operating_system' => true,
        ]);

        $esxRelease = Release::updateOrCreate(['software_id' => $esx->id, 'version' => '2024.01']);

        $ec2Instance = Baremetal::upsert(['name' => 'i - 5555a1a6666']);
        $ec2InstanceAssignment = ManagedBaremetal::updateOrCreate(['baremetal_id' => $ec2Instance->id, 'managed_offer_id' => $ec2->id, 'managed_account_id' => $awsProdAccount->id, 'availability_zone_id' => $eucentral1A->id]);

        $srv01 = Baremetal::upsert(['name' => 'DELL - 01 - VRT']);
        $srv02 = Baremetal::upsert(['name' => 'DELL - 02']);

        $srv01Host = Host::upsert(['name' => 'DELL - 01.esx'], [
            'operating_system_id' => $esxRelease->id,
            'virtualizer_id' => $esxRelease->id,
            'baremetal_id' => $srv01->id
        ]);

        $srv01Ex1 = Host::upsert(['name' => 'EX1'], [
            'operating_system_id' => $windows2019->id,
            'parent_host_id' => $srv01Host->id
        ]);

        $srv01Db1 = Host::upsert(['name' => 'DB1'], [
            'operating_system_id' => $windows2019->id,
            'parent_host_id' => $srv01Host->id
        ]);

        $devHost = Host::upsert(['name' => 'DEV'], [
            'operating_system_id' => $fedora2024->id,
            'parent_host_id' => $srv01Host->id
        ]);

        $devNic = Nic::updateOrCreate(['name' => 'eth0', 'mac_address' => 'FF:FF:FF', 'vlan_id' => $vlan->id, 'equipable_type' => 'Host', 'equipable_id' => $devHost->id]);
        $ipForDevNic = IpAddress::updateOrCreate(['ip_network_id' => $ipNetwork->id, 'address' => '10.0.0.1']);
        $devNic->ipAddresses()->attach($ipForDevNic->id);

        $dnsRecord = DnsRecord::updateOrCreate(['name' => 'router', 'dns_zone_id' => $dreitierZone->id, 'ip_address_id' => $devNic->id]);

        $gitlabOnDevHost = ApplicationInstance::upsert([
            'release_id' => $gitlabRelease->id,
            'executor_id' => $devHost->id,
            'executor_type' => 'host'
        ]);

        $postgresOnDevHost = ApplicationInstance::upsert([
            'release_id' => $postgresRelease->id,
            'executor_id' => $devHost->id,
            'executor_type' => 'host'
        ]);

        $srv01Db1Oracle = ApplicationInstance::upsert([
            'release_id' => $oracle19->id,
            'executor_id' => $srv01Db1->id,
            'executor_type' => 'host',], [
            'stage_id' => $stageProd->id,
        ]);

        $srv01Db2 = Host::upsert(['name' => 'DB2'], [
            'operating_system_id' => $windows2019->id,
            'parent_host_id' => $srv01Host->id
        ]);

        $srv01Db2Oracle = ApplicationInstance::upsert(['release_id' => $oracle19->id,
            'executor_id' => $srv01Db2->id,
            'executor_type' => 'host',
        ], [
            'stage_id' => $stageProd->id,
        ]);

        $exchange = ApplicationInstance::upsert([
            'release_id' => $exchange2019->id,
            'executor_id' => $srv01Ex1->id,
            'executor_type' => 'host'],
            [
                'stage_id' => $stageProd->id
            ]);

        $srv02Fedora = Host::upsert(['name' => 'k8s-master.node.local'], [
            'baremetal_id' => $srv02->id,
            'operating_system_id' => $fedora2024->id
        ]);

        $k8sRuntime = Runtime::upsert(['name' => 'k8s - local'], [
            'host_id' => $srv02Fedora->id,
            'release_id' => $kubernetesRelease->id,
        ]);

        $backmonDeployed = ApplicationInstance::upsert([
            'release_id' => $backmonLatest->id,
            'executor_type' => 'runtime',
            'executor_id' => $k8sRuntime->id
        ]);

        $databaseSchema = ResourceType::upsert('db-schema', ['name' => 'Database schema']);
        $messageQueue = ResourceType::upsert('message-queue', ['name' => 'Message queue']);

        $oracleCluster = Cluster::upsert(['name' => 'oracle - prod - cluster']);
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

        $devstack = Deployment::upsert(['name' => 'Devstack 1.0 deployment', 'release_train_id' => $devstackRT1_0->id]);
        $devstack->applicationInstances()->saveMany([
            $gitlabOnDevHost,
            $postgresOnDevHost,
        ]);

        $devstack->resources()->saveMany([
            $gitlabProdDatabase,
        ]);
    }
}
