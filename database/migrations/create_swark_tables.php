<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Swark\DataModel\Domain\Model\InformationTechnology\Component\ClusterMode;
use Swark\DataModel\Domain\Model\SoftwareArchitecture\UsageType;

return new class extends Migration {

    protected function createLifecycleTables()
    {
        // e.g. physical-hardware
        Schema::create('lifecycle_schema', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        // assign physical-hardware to ref_type:baremetal
        Schema::create('lifecycle_assigned', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lifecycle_schema_id');
            $table->string('ref_type');

            $table->foreign('lifecycle_schema_id')->references('id')->on('lifecycle_schema');
        });

        // reusable statusses, e.g. planned, purchased, online, offline, discarded
        Schema::create('lifecycle_status', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', \Swark\DataModel\Domain\Model\LifecycleStatusCategory::toMap())->default(\Swark\DataModel\Domain\Model\LifecycleStatusCategory::BEGIN->value);
        });

        // assign a lifecycle status to a specific schema
        Schema::create('lifecycle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lifecycle_schema_id');
            $table->unsignedBigInteger('lifecycle_status_id');
            // make that nullable so that we can apply the unique
            $table->boolean('is_start')->nullable()->default(null);

            $table->unique(['lifecycle_schema_id', 'lifecycle_status_id', 'is_start'], 'only_one_start_node');
            $table->foreign('lifecycle_schema_id')->references('id')->on('lifecycle_schema');
            $table->foreign('lifecycle_status_id')->references('id')->on('lifecycle_status');
        });

        // transition between statusses
        Schema::create('lifecycle_transition', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('from_lifecycle_id');
            $table->unsignedBigInteger('to_lifecycle_id');

            $table->unique(['from_lifecycle_id', 'to_lifecycle_id']);
            $table->foreign('from_lifecycle_id')->references('id')->on('lifecycle');
            $table->foreign('to_lifecycle_id')->references('id')->on('lifecycle');
        });
    }

    protected function createMetaTables()
    {
        Schema::create('meta_schema', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ref_type');
        });

        Schema::create('meta_type', function (Blueprint $table) {
            $table->id();
            $table->string('internal_name')->unique();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->boolean('is_nullable')->default(false);
            $table->enum('known_type', \Swark\DataModel\Domain\Model\Meta\KnownMetaType::toMap())->default(\Swark\DataModel\Domain\Model\Meta\KnownMetaType::STRING->value);
            $table->string('custom_type')->nullable(true);
        });

        // assign physical-hardware to ref_type:baremetal
        Schema::create('meta_property', function (Blueprint $table) {
            $table->id();
            $table->string('internal_name')->unique();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('meta_type_id');
            // if not present, use meta_type.is_nullable
            $table->boolean('is_nullable')->nullable();

            $table->foreign('meta_type_id')->references('id')->on('meta_type');

        });

        Schema::create('meta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meta_property_id');
            $table->unsignedBigInteger('meta_schema_id');
            $table->unsignedBigInteger('order_column')->default(0);
            $table->boolean('is_required')->default(false);

            $table->foreign('meta_property_id')->references('id')->on('meta_property');
            $table->foreign('meta_schema_id')->references('id')->on('meta_schema');
        });
    }

    protected function createConfigurationItemTables()
    {
        // aliases for configuration items, e.g. kubernetes-uid, jira, ipv4, ipv6
        Schema::create('naming_type', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('scomp_id')->unique();
            $table->string('public_format')->nullable();
            // nullable: can be overwritten by framework to make it distinguishable for specific types (e.g. private IPv4 addresses can be the same in different subnets)
            $table->boolean('is_unique_in_type')->nullable()->default(true);
        });

        Schema::create('configuration_item', function (Blueprint $table) {
            $table->string('ref_type');
            $table->unsignedBigInteger('ref_id');
            $table->uuid('uuid');
            $table->string('name')->nullable();
            $table->string('fullname')->nullable();
            $table->string('scomp_id')->nullable();
            $table->unsignedBigInteger('lifecycle_id')->nullable();
            $table->dateTime('last_seen_at')->nullable();
            $table->timestamps();

            $table->primary(['ref_type', 'ref_id']);
            $table->unique(['ref_type', 'scomp_id']);
            $table->unique(['uuid'], 'idx_by_uuid');
            $table->index(['ref_type', 'scomp_id'], 'idx_by_scomp_id');
            $table->index(['ref_type', 'ref_id'], 'idx_by_ref_id');
            $table->index(['uuid', 'ref_type', 'ref_id'], 'idx_full');
            $table->foreign('lifecycle_id')->references('id')->on('lifecycle');
        });

        // store additional names or ids for configuration items
        Schema::create('configuration_item_naming', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->unsignedBigInteger('configuration_item_uuid');
            $table->unsignedBigInteger('naming_type_id');
            $table->string('name');

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign(['uuid'])->references(['uuid'])->on('configuration_item')->onDelete('cascade');
            $table->foreign('naming_type_id')->references('id')->on('naming_type');
        });

        // additional meta data for configuration item
        Schema::create('configuration_item_meta', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->unsignedBigInteger('meta_id');
            // everything is nullable as the meta.meta_property defines its real type
            $table->longText('text_value')->nullable();
            $table->boolean('boolean_value')->nullable();
            $table->decimal('number_value', 10, 2)->nullable();
            $table->date('date_value')->nullable();

            $table->foreign(['uuid'])->references(['uuid'])->on('configuration_item')->onDelete('cascade');
            $table->foreign(['meta_id'])->references(['id'])->on('meta')->onDelete('cascade');
        });

        // event log for configuration items
        Schema::create('configuration_item_history', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('event');
            $table->longText('comment')->nullable();
            $table->longText('data')->nullable();
            $table->unsignedBigInteger('changed_by');
            $table->dateTime('changed_at');

            $table->foreign('uuid')->references('uuid')->on('configuration_item')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users');
        });
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->createLifecycleTables();
        $this->createMetaTables();
        $this->createConfigurationItemTables();

        Schema::create('content', function (Blueprint $table) {
            $table->id();
            $table->string('scomp_id');
            $table->longText('content');
            $table->enum('type', \Swark\Cms\Domain\Model\ContentType::toMap());
            $table->timestamps();
        });

        // Criticality
        Schema::create('criticality', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->smallInteger('position')->default(0);
        });

        Schema::create('scope_template', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('instance_of');
            $table->json('instance_parameters')->nullable();
            $table->json('template_options')->nullable();
        });

        Schema::create('regulation', function (Blueprint $table) {
            $table->id();
            // VaIT, NIS2, DORA
            $table->string('name');

            $table->timestamps();
            $table->softDeletesDatetime();
        });

        Schema::create('regulation_chapter', function (Blueprint $table) {
            $table->id();
            // chapter number
            $table->string('external_id')->nullable();
            // heading of this chapter
            $table->string('name');
            // content provided by officials
            $table->longText('official_content')->nullable();
            // comment to the official content
            $table->longtext('summary')->nullable();
            // current status
            $table->longtext('actual_status')->nullable();
            // target status
            $table->longtext('target_status')->nullable();
            // relevance
            $table->enum('relevancy', \Swark\DataModel\Domain\Model\Compliance\RelevanceType::toMap())->nullable();
            $table->unsignedBigInteger('regulation_id');

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('regulation_id')->references('id')->on('regulation');
        });

        Schema::create('regulation_control', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();
            $table->string('name');
            $table->longText('content')->nullable();
            $table->unsignedBigInteger('regulation_id');
            $table->unsignedBigInteger('regulation_chapter_id')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('regulation_id')->references('id')->on('regulation');
            $table->foreign('regulation_chapter_id')->references('id')->on('regulation_chapter');
            $table->unique(['regulation_id', 'external_id'], 'unq_regulation_control_external_id');
        });


        // strategy
        Schema::create('strategy', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->longText('description')->nullable();

            $table->timestamps();
        });

        Schema::create('objective', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // what is the goal of this objective
            $table->longText('description')->nullable();
            // why has this objective been introduced?
            $table->longText('reason')->nullable();
            $table->unsignedBigInteger('strategy_id');

            $table->timestamps();
            $table->foreign('strategy_id')->references('id')->on('strategy');
        });

        Schema::create('policy', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();

            $table->timestamps();
        });

        Schema::create('policy_for_regulation_chapter', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('regulation_chapter_id');
            $table->unsignedBigInteger('policy_id');

            $table->foreign('regulation_chapter_id')->references('id')->on('regulation_chapter');
            $table->foreign('policy_id')->references('id')->on('policy');
            $table->unique(['regulation_chapter_id', 'policy_id'], 'unq_policy_regulation_chapter');
        });

        Schema::create('rule', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('order_column')->default(0);

            $table->timestamps();

            $table->unsignedBigInteger('policy_id');

            $table->foreign('policy_id')->references('id')->on('policy');
        });

        Schema::create('rule_scope', function (Blueprint $table) {
            $table->id();
            $table->json('options')->nullable();
            $table->unsignedBigInteger('scope_template_id');
            $table->unsignedBigInteger('rule_id');

            $table->foreign('scope_template_id')->references('id')->on('scope_template');
            $table->foreign('rule_id')->references('id')->on('rule');
        });

        Schema::create('rule_scope_item', function (Blueprint $table) {
            $table->id();
            $table->morphs('item');
            $table->unsignedBigInteger('rule_scope_id');
            $table->enum('status', \Swark\DataModel\Domain\Model\Auditing\RuleStatus::toMap())->nullable();
            $table->dateTime('first_missing_at')->nullable();
            $table->dateTime('last_found_at')->nullable();
            $table->longText('description')->nullable();

            $table->timestamps();

            $table->foreign('rule_scope_id')->references('id')->on('rule_scope');
        });

        Schema::create('finding', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('criticality_id')->nullable();

            $table->string('name');
            $table->longText('description')->nullable();
            $table->enum('type', \Swark\DataModel\Domain\Model\Auditing\FindingType::toMap());
            $table->enum('status', \Swark\DataModel\Domain\Model\Auditing\Status::toMap());
            $table->longText('impact')->nullable();
            $table->longText('known_deficits')->nullable();
            $table->tinyInteger('probability')->nullable();
            $table->tinyInteger('extend_of_damage')->nullable();
            $table->enum('strategy', \Swark\DataModel\Domain\Model\Auditing\TreatmentStrategy::toMap());

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('criticality_id')->references('id')->on('criticality');
        });

        // a finding can belong to multiple controls in different regulations or objectives
        Schema::create('finding_assigned', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('finding_id');
            // control, objective
            $table->morphs('examinable');

            $table->timestamps();

            $table->foreign('finding_id')->references('id')->on('finding');
        });


        Schema::create('objective_for_regulation_chapter', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('objective_id');
            $table->unsignedBigInteger('chapter_id');

            $table->foreign('objective_id')->references('id')->on('objective');
            $table->foreign('chapter_id')->references('id')->on('regulation_chapter');
        });

        Schema::create('question', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->longText('description')->nullable();

            $table->timestamps();
        });

        Schema::create('question_in_strategy', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('strategy_id');

            $table->foreign('question_id')->references('id')->on('question');
            $table->foreign('strategy_id')->references('id')->on('strategy');
        });

        Schema::create('question_in_objective', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('objective_id');

            $table->foreign('question_id')->references('id')->on('question');
            $table->foreign('objective_id')->references('id')->on('objective');
        });

        Schema::create('question_for_regulation_chapter', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('chapter_id');

            $table->foreign('question_id')->references('id')->on('question');
            $table->foreign('chapter_id')->references('id')->on('regulation_chapter');
        });

        // measurements / metrics
        Schema::create('metric', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->enum('type', \Swark\DataModel\Domain\Model\Governance\Kpi\MetricType::toMap());
            // precision for percentage/decimal
            $table->tinyInteger('precision')->default(0);
            // what the goal value should be in the end: higher (yes), lower(no)
            $table->enum('goal_direction', \Swark\DataModel\Domain\Model\Governance\Kpi\GoalDirection::toMap());
            // can be measured as a KPI
            $table->boolean('is_measurable')->default(true);
            // can be used as a parameter for defining Business Continuity-related values for systems
            $table->boolean('is_system_parameter')->default(false);
        });

        Schema::create('kpi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('metric_id');
            $table->decimal('goal_value');
            $table->decimal('integer_threshold_1')->nullable();
            $table->decimal('integer_threshold_2')->nullable();
            $table->decimal('percentage_threshold_1')->nullable();
            $table->decimal('percentage_threshold_2')->nullable();

            $table->foreign('metric_id')->references('id')->on('metric');
        });

        Schema::create('kpi_assigned', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kpi_id');
            // risk, control, objective
            $table->morphs('measurable');

            $table->timestamps();

            $table->foreign('kpi_id')->references('id')->on('kpi');
        });

        Schema::create('measurement_period', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->date('begin_at');
            $table->date('end_at')->nullable();

            $table->timestamps();
        });

        Schema::create('measurement', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kpi_id');
            $table->unsignedBigInteger('measurement_period_id');
            $table->longText('description')->nullable();
            $table->decimal('goal_value')->nullable();
            $table->decimal('current_value');
            $table->boolean('is_goal_reached')->default(false);

            $table->timestamps();

            $table->foreign('kpi_id')->references('id')->on('kpi');
            $table->foreign('measurement_period_id')->references('id')->on('measurement_period');
        });

        // Action
        Schema::create('action', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->enum('status', \Swark\DataModel\Domain\Model\Auditing\Status::toMap());
            $table->date('begin_at')->nullable();
            $table->date('end_at')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();
        });

        Schema::create('action_assigned', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('action_id');
            // finding, control, objective
            $table->morphs('actionable');

            $table->timestamps();

            $table->foreign('action_id')->references('id')->on('action');
        });

        // IT
        Schema::create('organization', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // is internal organization or service provider and not us
            $table->boolean('is_internal')->default(false);
            // organization is vendor
            $table->boolean('is_vendor')->default(true);
            // organization is also a customer of us
            $table->boolean('is_customer')->default(false);
            // organization is service provider
            $table->boolean('is_managed_service_provider')->default(false);
            // NIS2: importance of organization
            $table->enum('importance', \Swark\DataModel\Domain\Model\Business\DependencyDegree::toMap())->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();
        });

        // types like logical_zone, vlan, baremetal, host, cluster, application_instance, managed_subscription can belong to organizations
        Schema::create('associated_with_organization', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->morphs('associatable', 'association_idx');
            $table->unsignedBigInteger('organization_id');
            $table->enum('role', \Swark\DataModel\Domain\Model\Business\AssociatedRole::toMap())->nullable();
        });

        // physical person, role or user group
        Schema::create('actor', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        Schema::create('technology', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default(\Swark\DataModel\Domain\Model\Governance\TechnologyType::OTHER->value);
        });

        Schema::create('technology_version', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_latest')->default(false);

            $table->unsignedBigInteger('technology_id');
            $table->foreign('technology_id')->references('id')->on('technology');
            $table->unique(['name', 'technology_id']);
        });

        Schema::create('resource_type', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('technology_version_id')->nullable();
            $table->foreign('technology_version_id')->references('id')->on('technology_version');
            $table->unique(['name', 'technology_version_id']);
        });

        Schema::create('artifact_type', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();

            $table->timestamps();
            $table->softDeletesDatetime();
        });

        Schema::create('architecture_type', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();

            $table->timestamps();
            $table->softDeletesDatetime();
        });

        // Data classification
        Schema::create('data_classification', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->smallInteger('position')->default(0);
        });

        // Protection goals
        Schema::create('protection_goal', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
        });

        // Protection goal level
        Schema::create('protection_goal_level', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->smallInteger('position')->default(0);

            $table->unsignedBigInteger('protection_goal_id');

            $table->unique(['name', 'protection_goal_id'], 'unq_protection_goal_level_name');
            $table->foreign('protection_goal_id')->references('id')->on('protection_goal');
        });

        // DMZ, Internet, Public, B2B
        Schema::create('logical_zone', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('data_classification_id')->nullable();

            $table->foreign('data_classification_id')->references('id')->on('data_classification');
        });

        Schema::create('actor_in_logical_zone', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_id');
            $table->unsignedBigInteger('logical_zone_id');
            $table->longText('description')->nullable();

            $table->foreign('actor_id')->references('id')->on('actor');
            $table->foreign('logical_zone_id')->references('id')->on('logical_zone');
        });

        // API, Service, Database, Application
        Schema::create('logical_layer', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        // NETWORK
        Schema::create('vlan', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number');
        });

        Schema::create('nic', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mac_address')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('vlan_id')->nullable();
            // ['host', 'baremetal']
            $table->morphs('equipable');

            $table->foreign('vendor_id')->references('id')->on('organization');
            $table->foreign('vlan_id')->references('id')->on('vlan');
        });

        Schema::create('ip_network', function (Blueprint $table) {
            $table->id();
            $table->enum('type', \Swark\DataModel\Domain\Model\InformationTechnology\Network\IpVersion::toMap());
            $table->string('network');
            // we need the string representation and binary. Laravel does not allow to make lookups via casts
            $table->binary('network_bin', length: 16);
            // don't use prefix as subnets can have holes in it
            $table->string('network_mask');
            // we need the string representation and binary. Laravel does not allow to make lookups via casts
            $table->binary('network_mask_bin', length: 16);
            // postpone gateway as table does not exist yet
            $table->longText('description')->nullable();

            $table->unsignedBigInteger('vlan_id')->nullable();

            $table->foreign('vlan_id')->references('id')->on('vlan');
        });

        Schema::create('ip_address', function (Blueprint $table) {
            $table->id();

            $table->string('address');
            // we need the string representation and binary. Laravel does not allow to make lookups via casts
            $table->binary('address_bin', length: 16);
            $table->unsignedBigInteger('ip_network_id')->nullable();
            $table->longText('description')->nullable();

            $table->foreign('ip_network_id')->references('id')->on('ip_network');
        });

        Schema::table('ip_network', function (Blueprint $table) {
            $table->unsignedBigInteger('gateway_id')->nullable();

            $table->foreign('gateway_id')->references('id')->on('ip_address');
        });

        Schema::create('ip_address_assigned', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ip_address_id');
            // - cluster: e.g. a load balancer with one IP
            // - nic: a directly assigned IP for a NIC
            // - application_instance: an application bound to a specific network interface's IP
            $table->morphs('assignable');
            $table->longText('description')->nullable();

            $table->foreign('ip_address_id')->references('id')->on('ip_address');
        });

        Schema::create('dns_zone', function (Blueprint $table) {
            $table->id();

            $table->string('zone')->unique();
            $table->unsignedBigInteger('parent_dns_zone_id')->nullable();

            $table->foreign('parent_dns_zone_id')->references('id')->on('dns_zone');
        });

        Schema::create('dns_record', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->unsignedBigInteger('dns_zone_id');
            // don't make it an enum so record type can be extended
            $table->string('type', 16);
            // either data or ip_address_id muss be filled
            $table->string('data')->nullable();
            $table->unsignedBigInteger('ip_address_id')->nullable();

            $table->foreign('dns_zone_id')->references('id')->on('dns_zone');
            $table->foreign('ip_address_id')->references('id')->on('ip_address');
        });

        Schema::create('dns_record_upstream', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('dns_record_id');
            // - application_instance: an application behind a proxy
            // - cluster: another proxy behind a front proxy, think of Rancher
            $table->morphs('upstreamable');
            $table->foreign('dns_record_id')->references('id')->on('dns_record');
        });

        // PROD, QS, TEST, LIVE, ...
        Schema::create('stage', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        Schema::create('software', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->enum('usage_type', UsageType::toMap());
            $table->boolean('is_virtualizer')->default(false);
            $table->boolean('is_operating_system')->default(false);
            $table->boolean('is_runtime')->default(false);
            $table->boolean('is_library')->default(false);
            // consists upon other software, e.g. a Helm install
            $table->boolean('is_bundle')->default(false);

            $table->unsignedBigInteger('business_criticality_id')->nullable();
            $table->unsignedBigInteger('infrastructure_criticality_id')->nullable();
            $table->unsignedBigInteger('logical_zone_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('artifact_type_id')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('business_criticality_id')->references('id')->on('criticality');
            $table->foreign('infrastructure_criticality_id')->references('id')->on('criticality');
            $table->foreign('logical_zone_id')->references('id')->on('logical_zone');
            $table->foreign('vendor_id')->references('id')->on('organization');
            $table->foreign('artifact_type_id')->references('id')->on('artifact_type');
        });

        Schema::create('source_provider', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // how can the source be resolved? e.g. http, http+git, artifacthub, ...
            $table->string('type');
            // root path to this source provider. e.g. github.com
            $table->string('path')->nullable();
            // configuration options
            $table->json('options')->nullable();
        });

        Schema::create('source', function (Blueprint $table) {
            $table->id();
            // what type of source is this? e.g. helm, code, container_image, ... if it is null, than software.artifact_type.name is used as a fallback
            $table->string('type')->nullable();
            // path to origin of this source. e.g. $org/$project for a source_configuration.provider=github or prometheus-community/prometheus-blackbox-exporter for an source_configuration.provider=artifacthub type
            $table->string('path');
            // configuration options
            $table->json('options')->nullable();

            // source provider to load defaults from
            $table->unsignedBigInteger('source_provider_id');
            $table->unsignedBigInteger('software_id');

            $table->foreign('software_id')->references('id')->on('software');
            $table->foreign('source_provider_id')->references('id')->on('source_provider');
        });

        Schema::create('release', function (Blueprint $table) {
            $table->id();
            $table->string('version');
            $table->boolean('is_latest')->default(false);
            // matches any release for this software
            $table->boolean('is_any')->default(false);
            $table->unsignedBigInteger('software_id');
            $table->longText('changelog')->nullable();
            $table->string('changelog_url')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('software_id')->references('id')->on('software');
            $table->unique(['version', 'software_id'], 'unq_version');
        });

        Schema::create('release_in_bundle', function (Blueprint $table) {
            $table->id();
            // release of bundle
            $table->unsignedBigInteger('release_bundle_id');
            // other release in this bundle
            $table->unsignedBigInteger('release_id');

            $table->foreign('release_bundle_id')->references('id')->on('release');
            $table->foreign('release_id')->references('id')->on('release');
        });

        Schema::create('artifact_in_release', function (Blueprint $table) {
            $table->id();
            $table->string('artifact_name')->nullable();
            // artifact can overwrite a release version
            $table->string('version')->nullable();

            $table->unsignedBigInteger('release_id');
            // if this is not specified, it is implicitly the artifact type of the parent release.software.artifact_type_id
            $table->unsignedBigInteger('artifact_type_id')->nullable();
            $table->unsignedBigInteger('architecture_type_id')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('artifact_type_id')->references('id')->on('artifact_type');
            $table->foreign('architecture_type_id')->references('id')->on('architecture_type');
            $table->foreign('release_id')->references('id')->on('release');
        });

        Schema::create('component', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();

            $table->unsignedBigInteger('software_id')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();
            $table->foreign('software_id')->references('id')->on('software');
            $table->unique(['name', 'software_id'], 'unq_name');
        });

        Schema::create('component_with_technology', function (Blueprint $table) {
            $table->id();
            $table->string('provider_consumer_type');
            $table->unsignedBigInteger('component_id');
            $table->unsignedBigInteger('technology_version_id');
            $table->foreign('component_id')->references('id')->on('component');
            $table->foreign('technology_version_id')->references('id')->on('technology_version');
        });

        Schema::create('service', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();

            $table->unsignedBigInteger('component_id');
            $table->foreign('component_id')->references('id')->on('component');
            $table->unique(['name', 'component_id']);
        });

        Schema::create('protocol_stack', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedBigInteger('application_layer_id');
            $table->unsignedBigInteger('presentation_layer_id')->nullable();
            $table->unsignedBigInteger('session_layer_id')->nullable();
            $table->unsignedBigInteger('transport_layer_id')->nullable();
            $table->unsignedBigInteger('network_layer_id')->nullable();
            $table->mediumInteger('port')->nullable();

            $table->foreign('application_layer_id')->references('id')->on('technology_version');
            $table->foreign('presentation_layer_id')->references('id')->on('technology_version');
            $table->foreign('session_layer_id')->references('id')->on('technology_version');
            $table->foreign('transport_layer_id')->references('id')->on('technology_version');
            $table->foreign('network_layer_id')->references('id')->on('technology_version');
        });

        Schema::create('service_interface', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('protocol_stack_id');
            $table->mediumInteger('port')->nullable();
            $table->foreign('service_id')->references('id')->on('service');
            $table->foreign('protocol_stack_id')->references('id')->on('protocol_stack');
        });

        // a software can span multiple layers e.g. frontend and backend and so on
        Schema::create('component_in_layer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('logical_layer_id')->nullable();
            $table->unsignedBigInteger('component_id')->nullable();

            $table->foreign('component_id')->references('id')->on('component');
            $table->foreign('logical_layer_id')->references('id')->on('logical_layer');

            $table->unique(['logical_layer_id', 'component_id']);
        });

        // structurizr/C4: software system - this is a blueprint for a deployment
        Schema::create('system', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('logical_zone_id')->nullable();
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->unsignedBigInteger('business_criticality_id')->nullable();
            $table->unsignedBigInteger('infrastructure_criticality_id')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('logical_zone_id')->references('id')->on('logical_zone');
            $table->foreign('stage_id')->references('id')->on('stage');
            $table->foreign('business_criticality_id')->references('id')->on('criticality');
            $table->foreign('infrastructure_criticality_id')->references('id')->on('criticality');
        });

        Schema::create('system_parameter', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('system_id');
            $table->unsignedBigInteger('metric_id');
            $table->decimal('value', 12, 6)->nullable();
            $table->longText('description')->nullable();

            $table->foreign('system_id')->references('id')->on('system');
            $table->foreign('metric_id')->references('id')->on('metric');
            $table->unique(['system_id', 'metric_id'], 'unq_system_parameter_');
        });

        Schema::create('system_in_protection_goal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('system_id');
            $table->unsignedBigInteger('protection_goal_id');
            // can be null if this protection goal is ignored
            $table->unsignedBigInteger('protection_goal_level_id')->nullable();
            $table->longText('description')->nullable();

            $table->foreign('system_id')->references('id')->on('system');
            $table->foreign('protection_goal_id')->references('id')->on('protection_goal');
            $table->foreign('protection_goal_level_id')->references('id')->on('protection_goal_level');
        });

        // a system can consist upon different elements
        Schema::create('system_element', function (Blueprint $table) {
            // resourcetype, software
            $table->morphs('element');
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('system_id');

            $table->timestamps();

            $table->foreign('system_id')->references('id')->on('system');
        });

        Schema::create('release_train', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_latest')->default(false);

            $table->timestamps();
            $table->unsignedBigInteger('system_id')->nullable();

            $table->foreign('system_id')->references('id')->on('system');
        });

        Schema::create('release_in_release_train', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('release_id');
            $table->unsignedBigInteger('release_train_id');

            $table->foreign('release_id')->references('id')->on('release');
            $table->foreign('release_train_id')->references('id')->on('release_train');
        });

        Schema::create('region', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('managed_service_provider_id');
            $table->foreign('managed_service_provider_id')->references('id')->on('organization');

            $table->unique(['name', 'managed_service_provider_id']);
        });

        Schema::create('availability_zone', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('region_id');
            $table->foreign('region_id')->references('id')->on('region');

            $table->unique(['name', 'region_id'], 'unq_availability_zone_name');
        });

        // e.g. Atlassian Jira, AWS Glacier
        Schema::create('managed_offer', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();

            $table->unsignedBigInteger('managed_service_provider_id');
            $table->unsignedBigInteger('software_id')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('managed_service_provider_id')->references('id')->on('organization');
            $table->foreign('software_id')->references('id')->on('software');

            $table->unique(['name', 'managed_service_provider_id'], 'unq_managed_offer_name');
        });

        Schema::create('managed_account', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();

            $table->unsignedBigInteger('managed_service_provider_id');

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('managed_service_provider_id')->references('id')->on('organization');

            $table->unique(['name', 'managed_service_provider_id']);
        });

        Schema::create('managed_subscription', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();

            $table->unsignedBigInteger('managed_offer_id');
            $table->unsignedBigInteger('managed_account_id');
            $table->unsignedBigInteger('logical_zone_id')->nullable();
            $table->unsignedBigInteger('availability_zone_id')->nullable();
            $table->unsignedBigInteger('release_id')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('managed_offer_id')->references('id')->on('managed_offer');
            $table->foreign('managed_account_id')->references('id')->on('managed_account');
            $table->foreign('logical_zone_id')->references('id')->on('logical_zone');
            $table->foreign('availability_zone_id')->references('id')->on('availability_zone');
            $table->foreign('release_id')->references('id')->on('release');

            $table->unique(['name', 'managed_offer_id', 'managed_account_id'], 'unq_managed_subscription_name');
        });

        Schema::create('baremetal', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();
        });

        Schema::create('managed_baremetal', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('baremetal_id');
            $table->unsignedBigInteger('managed_offer_id');
            $table->unsignedBigInteger('managed_account_id');
            $table->unsignedBigInteger('availability_zone_id');

            $table->foreign('baremetal_id')->references('id')->on('baremetal');
            $table->foreign('managed_offer_id')->references('id')->on('managed_offer');
            $table->foreign('managed_account_id')->references('id')->on('managed_account');
            $table->foreign('availability_zone_id')->references('id')->on('availability_zone');
        });

        Schema::create('cluster', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();

            // cluster is targeted for this software id and/or software version
            $table->unsignedBigInteger('target_release_id')->nullable();

            $table->enum('mode', ClusterMode::toMap())->nullable();
            $table->string('virtual_name')->nullable();
            // stage
            $table->unsignedBigInteger('stage_id')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('target_release_id')->references('id')->on('release');
            $table->foreign('stage_id')->references('id')->on('stage');
        });

        Schema::create('ip_network_assigned', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ip_network_id');
            // - cluster: e.g. a Kubernetes network
            // - application_instance: e.g. an application instance/deployment of a Kubernetes cluster assigned to multiple networks with Multus
            $table->morphs('assignable');
            $table->longText('description')->nullable();

            $table->foreign('ip_network_id')->references('id')->on('ip_network');
        });

        // a cluster can have 1 .. n members of different types
        Schema::create('namespace', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->unsignedBigInteger('cluster_id');
            $table->unsignedBigInteger('stage_id')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('cluster_id')->references('id')->on('cluster');
            $table->foreign('stage_id')->references('id')->on('stage');

            $table->unique(['name', 'cluster_id']);
        });

        // a cluster can have 1 .. n members of different types
        Schema::create('cluster_member', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cluster_id');

            // member type can be one of: Application instance, Runtime, Virtualized Host
            // A member can *not* be a Baremetal instance. Only software can be part of a cluster.
            $table->morphs('member');

            // cluster mode: is_primary can be different from is_active if a failover happened
            $table->boolean('is_primary')->nullable();
            $table->boolean('is_active')->nullable();

            // member of cluster may belong to namespace of this cluster
            $table->unsignedBigInteger('namespace_id')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('cluster_id')->references('id')->on('cluster');
            $table->foreign('namespace_id')->references('id')->on('namespace');

            $table->unique(['member_id', 'member_type', 'cluster_id', 'namespace_id'], 'unq_member_in_namespace');
        });

        Schema::create('service_in_cluster', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cluster_id');
            $table->unsignedBigInteger('service_id');

            $table->foreign('cluster_id')->references('id')->on('cluster');
            $table->foreign('service_id')->references('id')->on('service');
        });


        Schema::create('deployment', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedBigInteger('release_train_id')->nullable();
            $table->unsignedBigInteger('stage_id')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('release_train_id')->references('id')->on('release_train');
            $table->foreign('stage_id')->references('id')->on('stage');
        });

        Schema::create('deployment_element', function (Blueprint $table) {
            $table->id();
            // resource, subscription, application_instance
            $table->morphs('element');
            $table->unsignedBigInteger('deployment_id');

            $table->foreign('deployment_id')->references('id')->on('deployment');
        });

        Schema::create('service_path', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_interface_id');
            // ApplicationInstance, Cluster
            $table->morphs('provider_type');
            // ApplicationInstance, Cluster
            $table->morphs('consumer_type');
            $table->longText('description')->nullable();
            $table->foreign('service_interface_id')->references('id')->on('service_interface');
        });

        // a logical host; can either be running directly on hardware or inside a virtualized host
        Schema::create('host', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // host operating system environment
            $table->unsignedBigInteger('operating_system_id');
            // provide virtualization with this software
            $table->unsignedBigInteger('virtualizer_id')->nullable();
            // UC1: This host is part of a virtualized cluster and the parent host is *not* relevant:
            // This relationship is then realized inside the cluster_member table
            // UC2: It is relevant on which virtualization host *this* host runs:
            // if not null, then this host runs EXACTLY in the virtualization environment of the parent host id.
            // the parent_host_id must have virtualizer_id != null
            $table->unsignedBigInteger('parent_host_id')->nullable();
            // let's assume that by default the host has an affinity to run on the parent's host
            $table->boolean('has_parent_host_affinity')->default(true);

            // can be null if host is in virtualized environment
            $table->unsignedBigInteger('baremetal_id')->nullable();

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('operating_system_id')->references('id')->on('release');
            $table->foreign('virtualizer_id')->references('id')->on('release');
            $table->foreign('parent_host_id')->references('id')->on('host');
            $table->foreign('baremetal_id')->references('id')->on('baremetal');
        });

        // Databases, Queues, ...
        Schema::create('resource', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();

            $table->unsignedBigInteger('resource_type_id');
            // Cloud Offering, Cluster, Application Instance, Cloud Offer Instance
            $table->morphs('provider');

            $table->timestamps();
            $table->softDeletesDatetime();
        });

        Schema::create('runtime', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // host operating system environment
            $table->unsignedBigInteger('host_id');
            // software running this version
            $table->unsignedBigInteger('release_id');

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('host_id')->references('id')->on('host');
            $table->foreign('release_id')->references('id')->on('release');
        });

        Schema::create('application_instance', function (Blueprint $table) {
            $table->id();
            // an instance must be pin-pointed to a specific software release
            $table->unsignedBigInteger('release_id');
            // an instance can be inside a stage
            $table->unsignedBigInteger('stage_id')->nullable();
            // an instance can belong to a logical zone which differs from the software itself, e.g. for TEST instances
            $table->unsignedBigInteger('logical_zone_id')->nullable();
            // an instance can belong to exactly one instance. logical zone and stage must match the parent system
            $table->unsignedBigInteger('system_id')->nullable();
            // now it gets tricky:
            // 1. an instance may directly run a host as part of a binary file
            // 2. an instance can run inside a runtime (like a webapp, running in Apache Tomcat, IIS and so on)
            // 3. an instance may be part of a deployment over multiple host inside a cluster
            $table->morphs('executor');

            $table->timestamps();
            $table->softDeletesDatetime();

            $table->foreign('release_id')->references('id')->on('release');
            $table->foreign('stage_id')->references('id')->on('stage');
            $table->foreign('logical_zone_id')->references('id')->on('logical_zone');
            $table->foreign('system_id')->references('id')->on('system');
        });

        Schema::create('relationship_type', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('source_name');
            $table->string('target_name');
            $table->integer('port')->nullable();
            $table->unsignedBigInteger('protocol_stack_id')->nullable();

            $table->foreign('protocol_stack_id')->references('id')->on('protocol_stack');

            $table->boolean('is_restricting_types')->default(false);
        });

        Schema::create('relationship_type_constraint', function (Blueprint $table) {
            $table->id();

            $table->string('source_type');
            $table->string('target_type');
            $table->unsignedBigInteger('relationship_type_id')->nullable();

            $table->foreign('relationship_type_id')->references('id')->on('relationship_type');
        });

        Schema::create('relationship', function (Blueprint $table) {
            $table->id();

            // source class: ApplicationInstance, CloudOffering, Actor, System, Application, LogicalZone
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id');
            $table->string('source_name')->nullable();

            $table->enum('direction', \Swark\DataModel\Domain\Model\Meta\Direction::toMap());

            // target class: ApplicationInstance, CloudOffering
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id');
            $table->string('target_name')->nullable();

            $table->unsignedBigInteger('relationship_type_id')->nullable();

            $table->integer('port')->nullable();

            $table->unsignedBigInteger('protocol_stack_id')->nullable();
            $table->longText('description')->nullable();

            $table->foreign('protocol_stack_id')->references('id')->on('protocol_stack');
            $table->foreign('relationship_type_id')->references('id')->on('relationship_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ALM
        Schema::dropIfExists('relationship');
        Schema::dropIfExists('relationship_type');
        Schema::dropIfExists('relationship_type_constraint');

        // Deployment
        Schema::dropIfExists('application_instance');

        /* ✓ */
        Schema::dropIfExists('stage');

        // Infrastructure
        /* ✓ */
        Schema::dropIfExists('runtime');
        /* ✓ */
        Schema::dropIfExists('resource');
        /* ✓ */
        Schema::dropIfExists('host');
        /* ✓ */
        Schema::dropIfExists('cluster');
        /* ✓ */
        Schema::dropIfExists('baremetal');

        // Cloud
        /* ✓ */
        Schema::dropIfExists('managed_offer');
        /* ✓ */
        Schema::dropIfExists('availability_zone');
        /* ✓ */
        Schema::dropIfExists('region');
        /* ✓ */
        Schema::dropIfExists('logical_zone');

        // Software architecture
        /* ✓ */
        Schema::dropIfExists('system');
        /* ✓ */
        Schema::dropIfExists('software_in_layer');
        /* ✓ */
        Schema::dropIfExists('logical_layer');

        // Software inventory
        /* ✓ */
        Schema::dropIfExists('component');
        /* ✓ */
        Schema::dropIfExists('component_with_technology');
        /* ✓ */
        Schema::dropIfExists('release');
        /* ✓ */
        Schema::dropIfExists('software');
        /* ✓ */
        Schema::dropIfExists('artifact_in_release');
        /* ✓ */
        Schema::dropIfExists('artifact_type');

        /* ✓ */
        Schema::dropIfExists('technology');
        /* ✓ */
        Schema::dropIfExists('resource_type');

        // Actors/Externals
        /* ✓ */
        Schema::dropIfExists('organization');
        /* ✓ */
        Schema::dropIfExists('actor');

        // Risk
        /* ✓ */
        Schema::dropIfExists('risk');
        /* ✓ */
        Schema::dropIfExists('regulation_control');
        /* ✓ */
        Schema::dropIfExists('regulation_chapter');
        /* ✓ */
        Schema::dropIfExists('regulation');
    }
};
