<?php

namespace Swark\Tests\Integration\DataModel\Exchange;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Reader;
use PHPUnit\Framework\Attributes\Test;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Action;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Finding;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Policy;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Rule;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Template;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Actor;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Chapter;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Control;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\DataClassification;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\ProtectionGoal;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Regulation;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Criticality;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Kpi\Metric;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Kpi\Period;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy\Objective;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy\Question;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy\Strategy;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Technology;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Account;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Offer;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Subscription;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Baremetal;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Cluster;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Host;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Resource;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Runtime;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\System;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\ProtocolStack;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Zone;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\Relationship;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\ResourceType;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\ApplicationInstance;
use Swark\DataModel\Infrastructure\Eloquent\Model\Operations\Stage;
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\Software;
use Swark\DataModel\Infrastructure\Exchange\Excel\DataModelExcelFileFactory;
use Swark\DataModel\Infrastructure\Exchange\Excel\Import\DataModelExcelImport;
use Swark\DataModel\Infrastructure\Exchange\Excel\Import\DataModelImportOptions;
use Swark\DataModel\Infrastructure\Exchange\Excel\OrderedSheetExcelReader;
use Swark\DataModel\Infrastructure\Repository\Scope\ItemsByScompId;
use Swark\Tests\IntegrationTestCase;

class ImportIntegrationTest extends IntegrationTestCase
{
    use DatabaseTransactions;

    private function sut(): void
    {
        $factory = app()->make(DataModelExcelFileFactory::class);
        app()->bind(Reader::class, OrderedSheetExcelReader::class);
        $options = new DataModelImportOptions(
            pathToImportDirectory: __DIR__ . '/testdata-datamodel-excel/integration_test_data.xlsx',
            markdownOnly: false,
            enabledImportCategories: DataModelImportOptions::getAvailableImportCategories(),
        );

        $swarkExcelImporter = new DataModelExcelImport(
            dataModelExcelFile: $factory->createForImport(),
            options: $options
        );

        Excel::import($swarkExcelImporter, $options->excelFilePath()->getRealPath());
    }

    #[Test]
    public function import_technology(): void
    {
        $this->sut();

        $first = Technology::where('name', 'PostgreSQL')->firstOrFail();;
        $this->assertEquals('PostgreSQL', $first->name);
        $this->assertEquals('postgres', $first->configurationItem->scomp_id);
        $this->assertEquals('protocol', $first->type);
        // also has last version
        $this->assertEquals(1, $first->versions->count());

        $last = Technology::where('name', 'NFS')->firstOrFail();;
        $this->assertEquals('NFS', $last->name);
        $this->assertEquals('nfs', $last->configurationItem->scomp_id);
        $this->assertEquals('protocol', $last->type);
    }

    #[Test]
    public function import_criticality(): void
    {
        $this->sut();

        $first = Criticality::where('name', 'Niedrig')->firstOrFail();;
        $this->assertEquals('Niedrig', $first->name);
        $this->assertEquals('low', $first->configurationItem->scomp_id);
        $this->assertEquals('1', $first->position);

        $last = Criticality::where('name', 'Sehr hoch')->firstOrFail();;
        $this->assertEquals('Sehr hoch', $last->name);
        $this->assertEquals('very_high', $last->configurationItem->scomp_id);
        $this->assertEquals('4', $last->position);
    }

    #[Test]
    public function import_protection_goal_and_levels(): void
    {
        $this->sut();

        $first = ProtectionGoal::where('name', 'Integrität')->firstOrFail();;
        $this->assertEquals('Integrität', $first->name);
        $this->assertEquals('integrity', $first->configurationItem->scomp_id);
        $this->assertEquals(3, $first->protectionGoalLevels->count());

        $last = ProtectionGoal::where('name', 'Verlässlichkeit')->firstOrFail();;
        $this->assertEquals('Verlässlichkeit', $last->name);
        $this->assertEquals('reliability', $last->configurationItem->scomp_id);
        $this->assertEquals(3, $last->protectionGoalLevels->count());
    }

    #[Test]
    public function import_data_classification(): void
    {
        $this->sut();

        $first = DataClassification::where('name', 'Öffentlich')->firstOrFail();;
        $this->assertEquals('Öffentlich', $first->name);
        $this->assertEquals('public', $first->configurationItem->scomp_id);
        $this->assertNotEmpty($first->description);

        $last = DataClassification::where('name', 'Geheim')->firstOrFail();;
        $this->assertEquals('Geheim', $last->name);
        $this->assertEquals('secret', $last->configurationItem->scomp_id);
        $this->assertNotEmpty($last->description);
    }

    #[Test]
    public function import_actors(): void
    {
        $this->sut();

        $first = Actor::where('name', 'Jeder')->firstOrFail();;
        $this->assertEquals('Jeder', $first->name);
        $this->assertEquals('any', $first->configurationItem->scomp_id);

        $last = Actor::where('name', 'Entwicklungsteam')->firstOrFail();;
        $this->assertEquals('Entwicklungsteam', $last->name);
        $this->assertEquals('entwicklungsteam', $last->configurationItem->scomp_id);
    }

    #[Test]
    public function import_logical_zones(): void
    {
        $this->sut();

        $first = Zone::where('name', 'PUBLIC')->firstOrFail();;
        $this->assertEquals('PUBLIC', $first->name);
        $this->assertEquals('public', $first->configurationItem->scomp_id);
        $this->assertNotEmpty($first->description);
        $this->assertEquals('public', $first->dataClassification->configurationItem->scomp_id);
        $this->assertEquals('any', $first->actors->first()->configurationItem->scomp_id);

        // b2x zone
        $this->assertNotEmpty(Relationship::where('source_id', $first->id)->where('source_type', 'logical_zone')->get());

        $last = Zone::where('name', 'INFRA')->firstOrFail();;
        $this->assertEquals('INFRA', $last->name);
        $this->assertEquals('infra', $last->configurationItem->scomp_id);
        $this->assertNotEmpty($last->description);
        $this->assertEquals('confidential', $last->dataClassification->configurationItem->scomp_id);
        $this->assertEquals('it', $last->actors->first()->configurationItem->scomp_id);
    }

    #[Test]
    public function import_stages(): void
    {
        $this->sut();

        $first = Stage::where('name', 'DEV')->firstOrFail();;
        $this->assertEquals('DEV', $first->name);
        $this->assertEquals('dev', $first->configurationItem->scomp_id);

        $last = Stage::where('name', 'PROD')->firstOrFail();;
        $this->assertEquals('PROD', $last->name);
        $this->assertEquals('prod', $last->configurationItem->scomp_id);
    }

    #[Test]
    public function import_protocol_stacks(): void
    {
        $this->sut();

        $first = ProtocolStack::where('name', 'HTTP')->firstOrFail();;
        $this->assertEquals('HTTP', $first->name);
        $this->assertEquals(80, $first->port);
        $this->assertEquals('any', $first->applicationLayer->technology->configurationItem->scomp_id);
        $this->assertEquals('http', $first->presentationLayer->technology->configurationItem->scomp_id);
        $this->assertEquals('http', $first->sessionLayer->technology->configurationItem->scomp_id);
        $this->assertEquals('tcp', $first->transportLayer->technology->configurationItem->scomp_id);
        $this->assertEquals('ip', $first->networkLayer->technology->configurationItem->scomp_id);

        $last = ProtocolStack::where('name', 'SMTPS')->firstOrFail();;
        $this->assertEquals('SMTPS', $last->name);
        $this->assertEquals(687, $last->port);
        $this->assertEquals('smtp', $last->applicationLayer->technology->configurationItem->scomp_id);
        $this->assertEquals('smtp', $last->presentationLayer->technology->configurationItem->scomp_id);
        $this->assertEquals('tls', $last->sessionLayer->technology->configurationItem->scomp_id);
        $this->assertEquals('tcp', $last->transportLayer->technology->configurationItem->scomp_id);
        $this->assertEquals('ip', $last->networkLayer->technology->configurationItem->scomp_id);
    }

    #[Test]
    public function import_resource_types(): void
    {
        $this->sut();

        $first = ResourceType::where('name', 'MSSQL database schema')->firstOrFail();;
        $this->assertEquals('MSSQL database schema', $first->name);
        $this->assertEquals('mssql-db-schema', $first->configurationItem->scomp_id);
        $this->assertEquals('mssql', $first->technologyVersion->technology->configurationItem->scomp_id);

        $last = ResourceType::where('name', 'Webservice')->firstOrFail();;
        $this->assertEquals('Webservice', $last->name);
        $this->assertEquals('webservice', $last->configurationItem->scomp_id);
        $this->assertNull($last->technologyVersion);
    }

    #[Test]
    public function import_software(): void
    {
        $this->sut();

        $first = Software::where('name', 'Active Directory')->firstOrFail();;
        $this->assertEquals('Active Directory', $first->name);
        $this->assertEquals('ad', $first->configurationItem->scomp_id);
        $this->assertEquals('Microsoft', $first->vendor->name);
        $this->assertEquals('1.0', $first->latest()->version);

        $vmware = Software::where('name', 'VMWare ESXi')->firstOrFail();
        $this->assertTrue($vmware->is_virtualizer);
        $this->assertTrue($vmware->is_operating_system);

        $last = Software::where('name', 'Kubernetes')->firstOrFail();;
        $this->assertEquals('Kubernetes', $last->name);
        $this->assertEquals('kubernetes', $last->configurationItem->scomp_id);
        $this->assertTrue($last->is_runtime);
    }

    #[Test]
    public function import_systems(): void
    {
        $this->sut();

        $first = System::where('name', 'Devstack')->firstOrFail();;
        $this->assertEquals('Devstack', $first->name);
        $this->assertEquals('Development environment', $first->description);
        $this->assertEquals('devstack-prod', $first->configurationItem->scomp_id);
        $this->assertEquals('prod', $first->stage->configurationItem->scomp_id);
        $this->assertEquals('intern', $first->zone->configurationItem->scomp_id);
        $this->assertEquals('very_high', $first->businessCriticality->configurationItem->scomp_id);
        $this->assertEquals('low', $first->infrastructureCriticality->configurationItem->scomp_id);

        $last = System::where('name', 'IdP')->firstOrFail();;
        $this->assertEquals('IdP', $last->name);
        $this->assertEquals('idp-prod', $last->configurationItem->scomp_id);
    }

    #[Test]
    public function import_releases(): void
    {
        $this->sut();

        $first = Software::byScompId('rancher');

        $this->assertEquals('2.8.2', $first->latest()->version);

        $last = Software::byScompId('kubernetes');
        // 1.22.8, 1.26.11, 1.26.14, 1.27.6, 1.28.7,  latest
        $this->assertEquals(6, $last->releases->count());
        $this->assertEquals('1.28.7', $last->latest()->version);
    }

    #[Test]
    public function import_managed_accounts(): void
    {
        $this->sut();

        $first = Account::byScompId('azure-prod');
        $this->assertEquals('Microsoft', $first->managedServiceProvider->name);
        $this->assertEquals('default', $first->name);

        $last = Account::byScompId('hetzner-prod');
        $this->assertEquals('Hetzner', $last->managedServiceProvider->name);
        $this->assertEquals('default', $last->name);
    }

    #[Test]
    public function import_managed_offers(): void
    {
        $this->sut();

        $first = Offer::byScompId('ms-entra-id');
        $this->assertEquals('Microsoft', $first->managedServiceProvider->name);
        $this->assertEquals('Entra ID', $first->name);

        $last = Offer::byScompId('hetzner-root-server');
        $this->assertEquals('Hetzner', $last->managedServiceProvider->name);
        $this->assertEquals('Root Server', $last->name);
    }

    #[Test]
    public function import_managed_subscriptions(): void
    {
        $this->sut();

        $first = Subscription::where('name', 'azure-prod-subscription')->firstOrFail();;
        $this->assertEquals('azure-prod', $first->account->configurationItem->scomp_id);
        $this->assertEquals('Entra ID', $first->offer->name);

        $last = Subscription::where('name', 'sophos-central-subscription')->firstOrFail();;
        $this->assertEquals('sophos-prod', $last->account->configurationItem->scomp_id);
        $this->assertEquals('Sophos Central', $last->offer->name);
    }

    #[Test]
    public function import_baremetals(): void
    {
        $this->sut();

        $first = Baremetal::where('name', 'esx1')->firstOrFail();;
        $this->assertEquals('ESX 1 server', $first->description);
        $this->assertEquals('esx1', $first->name);

        $last = Baremetal::where('name', 'marks')->firstOrFail();;
        $this->assertEquals('Hetzner', $last->description);
        $this->assertEquals('marks', $last->name);
        $this->assertEquals('Root Server', $last->managed->offer->name);
        $this->assertEquals('Root Server', $last->managed->offer->name);
        $this->assertEquals('hetzner-prod', $last->managed->account->configurationItem->scomp_id);
        $this->assertEquals('eu-central-1', $last->managed->availabilityZone->region->name);
        $this->assertEquals('a', $last->managed->availabilityZone->name);
    }

    /**
     * We have to import the cluster definitions first, then the actual members and at last the cluster member assignments
     * @return void
     */
    #[Test]
    public function import_cluster_definitions(): void
    {
        $this->sut();

        $first = Cluster::where('name', 'esx-prod')->firstOrFail();;
        $this->assertEquals('esx-prod', $first->name);
        $this->assertEquals('prod', $first->stage->configurationItem->scomp_id);

        $last = Cluster::where('name', 'hyperv-test')->firstOrFail();;
        $this->assertEquals('hyperv-test', $last->name);
        $this->assertEquals('test', $last->stage->configurationItem->scomp_id);
    }

    #[Test]
    public function has_cluster_members_assigned(): void
    {
        $this->sut();

        $first = Cluster::where('name', 'esx-prod')->firstOrFail();
        $this->assertEquals(2, $first->hosts->count());

        $last = Cluster::where('name', 'hyperv-test')->firstOrFail();;
        $this->assertEquals(1, $last->hosts->count());
    }

    #[Test]
    public function import_hosts(): void
    {
        $this->sut();

        $first = Host::where('name', 'esx1.internal')->firstOrFail();
        $this->assertEquals('esx1', $first->baremetal->configurationItem->scomp_id);

        $onHyperV = Host::where('name', 'dc1.internal')->firstOrFail();
        $this->assertEquals('hv', $onHyperV->baremetal->configurationItem->scomp_id);

        $last = Host::where('name', 'k8s-worker-2-vm.internal')->firstOrFail();
        $this->assertEquals('esx2', $last->parentHost->configurationItem->scomp_id);
    }

    #[Test]
    public function import_runtimes(): void
    {
        $this->sut();

        $first = Runtime::byScompId('k8s-worker-1-runtime-prod');
        $this->assertEquals('k8s-worker-1-vm', $first->host->configurationItem->scomp_id);
        $this->assertEquals('1.27.6', $first->release->version);

        $last = Runtime::byScompId('k8s-worker-2-runtime-prod');
        $this->assertEquals('k8s-worker-2-vm', $last->host->configurationItem->scomp_id);
        $this->assertEquals('1.27.6', $last->release->version);
    }

    #[Test]
    public function import_application_instances(): void
    {
        $this->sut();

        $first = ApplicationInstance::byScompId('ad-prod');
        $this->assertEquals('INFRA', $first->zone->name);
        $this->assertEquals('dc1', $first->executor->configurationItem->scomp_id);

        $last = ApplicationInstance::byScompId('rancher-infra');

        $this->assertEquals('INFRA', $last->zone->name);
        $this->assertEquals('kubernetes-prod', $last->executor->configurationItem->scomp_id);
    }

    #[Test]
    public function import_resource_usages(): void
    {
        // given
        $this->sut();

        $databaseSchemaType = ResourceType::byScompId('db-schema');

        $this->assertNotNull($databaseSchemaType->id);

        // when
        $first = Resource::where('name', 'wsus')->firstOrFail();
        // then
        $this->assertEquals('wsus', $first->name);
        $this->assertEquals($databaseSchemaType->id, $first->resourceType->id);
        $this->assertEquals('mssql-wsus-prod', $first->provider->configurationItem->scomp_id);
    }

    #[Test]
    public function import_resource_usages_alsoUpdateRelationships(): void
    {
        // given
        $this->sut();
        $databaseSchemaType = ResourceType::byScompId('db-schema');
        $wsusDatabaseSchema = Resource::where('name', 'wsus')->firstOrFail();
        $wsusInstance = ApplicationInstance::byScompId('wsus-prod');
        $mssqlSrvApplicationInstance = ApplicationInstance::byScompId('mssql-wsus-prod');

        // when
        $first = Relationship::where('source_type', 'application_instance')
            ->where('source_id', $wsusInstance->id)
            ->where('target_id', $wsusDatabaseSchema->id)
            ->where('target_type', 'resource')->firstOrFail();


        // then
        $this->assertGreaterThan(0, $first->relationship_type_id);
    }

    #[Test]
    public function import_connections(): void
    {
        // given
        $this->sut();
        $rancher = ApplicationInstance::byScompId('rancher-infra');
        $protocolStackSsh = ProtocolStack::where('name', 'ssh')->firstOrFail();
        $protocolStackHttps = ProtocolStack::where('name', 'https')->firstOrFail();
        $targetCluster = Cluster::byScompId('kubernetes-prod');

        // when
        $all = Relationship::where('source_type', 'application_instance')
            ->where('source_id', $rancher->id)
            ->get();

        // then
        $this->assertGreaterThanOrEqual(2, $all->count());
        $this->assertEquals($protocolStackSsh->id, $all->get(0)->protocol_stack_id);
        $this->assertEquals(22, $all->get(0)->port);
        $this->assertEquals($targetCluster->id, $all->get(0)->target_id);
        $this->assertEquals($protocolStackHttps->id, $all->get(1)->protocol_stack_id);
        $this->assertEquals(8443, $all->get(1)->port);
        $this->assertEquals($targetCluster->id, $all->get(1)->target_id);
    }

    #[Test]
    public function import_regulations(): void
    {
        // given
        $this->sut();

        // when
        $first = Regulation::byScompId('dora');
        $last = Regulation::byScompId('tisax');

        // then
        $this->assertEquals('DORA', $first->name);
        $this->assertEquals('TISAX', $last->name);
    }

    #[Test]
    public function import_regulation_chapters(): void
    {
        // given
        $this->sut();

        $vait = Regulation::byScompId('vait');
        // when
        $first = Chapter::where('external_id', '1.2.d')->where('regulation_id', $vait->id)->firstOrFail();
        $last = Chapter::where('external_id', '8.1ff')->where('regulation_id', $vait->id)->firstOrFail();

        // then
        $this->assertEquals('IT-Strategie > strategische Entwicklung der IT-Architektur', $first->name);
        $this->assertNotEmpty($first->official_content);
        $this->assertNotEmpty($first->actual_status);
        $this->assertNotEmpty($first->target_status);
        $this->assertEquals('IT-Betrieb', $last->name);
    }

    #[Test]
    public function import_regulation_controls(): void
    {
        // given
        $this->sut();

        $vait = Regulation::byScompId('vait');
        // when
        $first = Control::where('external_id', 'vait-plan-control')->where('regulation_id', $vait->id)->firstOrFail();
        $last = Control::where('external_id', 'ff')->where('regulation_id', $vait->id)->firstOrFail();

        // then
        $this->assertEquals('Es wurde ein Plan aufgestellt, um eine IT-Architektur einzuführen', $first->name);
        $this->assertEquals('1.2.d', $first->chapter->external_id);
        $this->assertNotEmpty($first->content);
        $this->assertEquals('Die VaIT wurde analyisiert', $last->name);
    }


    #[Test]
    public function import_strategies(): void
    {
        // given
        $this->sut();

        // when
        $first = Strategy::byScompId('2029');

        // then
        $this->assertEquals('Strategie bis 2029', $first->name);
    }

    #[Test]
    public function import_strategy_questions(): void
    {
        // given
        $this->sut();
        $strategy = Strategy::byScompId('2029');

        // when
        $first = Question::byScompId('it-landschaft');
        $last = Question::byScompId('techstack');

        // then
        $this->assertEquals('Wie soll die IT-Landschaft gestaltet sein?', $first->name);
        $this->assertEquals($strategy->id, $first->strategies()->first()->id);
        $this->assertEquals('Welches Technologiestack ist sinnvoll?', $last->name);
        $this->assertEquals($strategy->id, $last->strategies()->first()->id);
    }

    #[Test]
    public function import_strategy_objectives(): void
    {
        // given
        $this->sut();
        $strategy = Strategy::byScompId('2029');
        $strategyQuestionItLandscape = Question::byScompId('it-landschaft');
        $strategyQuestionTechstack = Question::byScompId('techstack');

        // when
        $first = Objective::byScompId('strukturierung');
        $last = Objective::byScompId('compliance');

        // then
        $this->assertEquals('Strukturierung description', $first->description);
        $this->assertEquals('Strukturierung reason', $first->reason);
        $this->assertEquals($strategy->id, $first->strategy_id);
        $this->assertEquals($strategyQuestionItLandscape->id, $first->questions()->first()->id);
        $this->assertEquals($strategyQuestionTechstack->id, $first->questions()->get()->pop()->id);

        $this->assertEquals('Compliance description', $last->description);
        $this->assertEquals('Compliance reason', $last->reason);
        $this->assertEquals($strategy->id, $last->strategy_id);
        $this->assertEquals($strategyQuestionTechstack->id, $last->questions()->first()->id);
    }

    #[Test]
    public function import_measurement_periods(): void
    {
        // given
        $this->sut();

        // when
        $first = Period::byScompId('2025_1');
        $last = Period::byScompId('2025_2');

        $this->assertEquals('2025 1. Halbjahr', $first->name);
        $this->assertEquals('Description 2025/1', $first->description);
        $this->assertEquals('2025-01-01', $first->begin_at);
        $this->assertEquals('2025-06-30', $first->end_at);
        $this->assertEquals('2025 2. Halbjahr', $last->name);
    }

    #[Test]
    public function import_scope_templates(): void
    {
        // given
        $this->sut();

        // when
        $first = Template::byScompId('logical-zones-by-scomp-id');

        $this->assertEquals('Logische Zonen anhand der Scomp-ID', $first->name);
        $this->assertEquals('Liefert eine Zone anhand ihrer Scomp-ID zurück', $first->description);
        $this->assertEquals(ItemsByScompId::class, $first->instance_of);
        $this->assertEquals(['type' => 'logical_zone'], $first->instance_parameters);
    }

    #[Test]
    public function import_policies(): void
    {
        // given
        $this->sut();

        // when
        $first = Policy::byScompId('authn');
        $last = Policy::byScompId('authz');

        $this->assertEquals('Richtlinie für Authentifizierung', $first->name);
        $this->assertNotEmpty($first->description);
        $this->assertEquals('Richtlinie für Autorisierung', $last->name);
    }


    #[Test]
    public function import_rules(): void
    {
        // given
        $this->sut();
        $policy = Policy::byScompId('authn');

        // when
        $first = Rule::byScompId('authn-b2x');

        $this->assertEquals('Autentifizierung für Anwendungen im B2X-Bereich', $first->name);
        $this->assertEquals($policy->id, $first->policy_id);
        $this->assertEquals('Anwendungen innerhalb des B2X-Bereichs müssen 2FA-Authentifizierung implementieren', $first->description);
        $this->assertEquals(1, $first->scopes->count());
        $this->assertEquals(["scomp_ids" => ["b2x", "test"]], $first->scopes->first()->options);
    }

    #[Test]
    public function import_findings(): void
    {
        // given
        $this->sut();

        $objective = Objective::byScompId('strukturierung');
        $criticality = Criticality::byScompId('low');
        // when
        $first = Finding::byScompId('dont-switch-from-jira-to-swark');

        $this->assertEquals('Keep the lights on for Jira', $first->name);
        $this->assertEquals('swark is not a replacement for Atlassian Jira. Do not try to shut Jira down.', $first->description);
        $this->assertEquals($objective->id, $first->objectives()->first()->id);
        $this->assertEquals($criticality->id, $first->criticality->id);
    }

    #[Test]
    public function import_actions(): void
    {
        // given
        $this->sut();

        $objective = Objective::byScompId('strukturierung');
        $criticality = Criticality::byScompId('low');
        // when
        $first = Action::byScompId('setup-swark');

        $this->assertEquals('Setup swark', $first->name);
        $this->assertEquals('swark must be set up to make it easier to understand', $first->description);
        $this->assertEquals($objective->id, $first->objectives()->first()->id);
        $this->assertEquals('2024-06-01', $first->begin_at->toDateString());
        $this->assertEquals('2024-06-29', $first->end_at->toDateString());
    }


    #[Test]
    public function import_metrics(): void
    {
        // given
        $this->sut();

        $action = Action::byScompId('setup-swark');

        // when
        $data = Metric::all();
        $first = $data->first();
        $firstKpi = $first->kpis->first();
        $firstMeasurement = $firstKpi->measurements->first();
        $last = $data->pop();

        $this->assertEquals('Installation status of swark', $first->name);
        $this->assertEquals('percentage', $first->type);
        $this->assertEquals('higher', $first->goal_direction);
        $this->assertEquals(1, $first->is_measurable);
        $this->assertEquals(0, $first->is_system_parameter);
        $this->assertEquals(100, $firstKpi->goal_value);
        $this->assertEquals(50, $firstKpi->percentage_threshold_1);
        $this->assertEquals(80, $firstKpi->percentage_threshold_2);
        $this->assertEquals($action->id, $firstKpi->actions->first()->id);
        $this->assertEquals(20, $firstMeasurement->current_value);

        $this->assertEquals('Verfügbarkeit', $last->name);
        $this->assertEquals(6, $last->precision);
    }
}
