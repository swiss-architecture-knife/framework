<?php

namespace Swark\DataModel\Infrastructure\Eloquent\Model;

use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Action;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Finding;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Policy;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Rule;
use Swark\DataModel\Infrastructure\Eloquent\Model\Auditing\Template;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Actor;
use Swark\DataModel\Infrastructure\Eloquent\Model\Business\Organization;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Control;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\DataClassification;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\ProtectionGoal;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\ProtectionGoalLevel;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Regulation;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Criticality;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Kpi\Metric;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Kpi\Period;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy\Objective;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy\Question;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Strategy\Strategy;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\Technology;
use Swark\DataModel\Infrastructure\Eloquent\Model\Governance\TechnologyVersion;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\ArchitectureType;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Account;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\AvailabilityZone;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Offer;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Region;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Cloud\Subscription;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Baremetal;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Cluster;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Host;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Resource;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\Runtime;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Component\System;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\DnsRecord;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\DnsZone;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\IpNetwork;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\Nic;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Network\Vlan;
use Swark\DataModel\Infrastructure\Eloquent\Model\InformationTechnology\Zone;
use Swark\DataModel\Infrastructure\Eloquent\Model\Meta\RelationshipType;
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
use Swark\DataModel\Infrastructure\Eloquent\Model\SoftwareArchitecture\SourceProvider;

enum ModelTypes: string
{
    case Organization = 'organization';
    case Host = 'host';

    case Runtime = 'runtime';

    case Baremetal = 'baremetal';

    case Subscription = 'subscription';

    case Region = 'region';

    case AvailabilityZone = 'az';

    case Account = 'account';

    case Offer = 'offer';

    case Cluster = 'cluster';

    case ApplicationInstance = 'application_instance';

    case Resource = 'resource';

    case Deployment = 'deployment';

    case Software = 'software';

    case ResourceType = 'resource_type';

    case ArchitectureType = 'architecture_type';

    case Actor = 'actor';

    case System = 'system';

    case Service = 'service';

    case Component = 'component';

    case Finding = 'finding';

    case Objective = 'objective';

    case Control = 'control';

    case LogicalZone = 'logical_zone';

    case Layer = 'layer';

    case Action = 'action';

    case Vlan = 'vlan';

    case IpNetwork = 'ip_network';

    case DnsZone = 'dns_zone';

    case DnsRecord = 'dns_record';
    case Nic = 'nic';

    case ArtifactType = 'artifact_type';

    case Technology = 'technology';

    case TechnologyVersion = 'technology_version';

    case Criticality = 'criticality';

    case SourceProvider = 'source_provider';

    case Regulation = 'regulation';

    case Stage = 'stage';

    case ProtectionGoal = 'protection_goal';

    case ProtectionGoalLevel = 'protection_goal_level';

    case DataClassification = 'data_classification';

    case AuditingTemplate = 'auditing_template';

    case Policy = 'policy';
    case AuditingRule = 'auditing_rule';

    case Strategy = 'strategy';

    case StrategyQuestion = 'strategy_question';

    case Period = 'period';
    case RelationshipType = 'relationship_type';
    case Release = 'release';
    case Metric = 'metric';

    public function modelClass(): string
    {
        return match ($this) {
            ModelTypes::Account => Account::class,
            ModelTypes::Offer => Offer::class,
            ModelTypes::Organization => Organization::class,
            ModelTypes::Host => Host::class,
            ModelTypes::AvailabilityZone => AvailabilityZone::class,
            ModelTypes::Runtime => Runtime::class,
            ModelTypes::Region => Region::class,
            ModelTypes::Baremetal => Baremetal::class,
            ModelTypes::Subscription => Subscription::class,
            ModelTypes::Cluster => Cluster::class,
            ModelTypes::ApplicationInstance => ApplicationInstance::class,
            ModelTypes::Resource => Resource::class,
            ModelTypes::Deployment => Deployment::class,
            ModelTypes::Software => Software::class,
            ModelTypes::ResourceType => ResourceType::class,
            ModelTypes::ArchitectureType => ArchitectureType::class,
            ModelTypes::ArtifactType => ArtifactType::class,
            ModelTypes::Actor => Actor::class,
            ModelTypes::System => System::class,
            ModelTypes::Service => Service::class,
            ModelTypes::Component => Component::class,
            ModelTypes::Finding => Finding::class,
            ModelTypes::Objective => Objective::class,
            ModelTypes::Layer => Layer::class,
            ModelTypes::Control => Control::class,
            ModelTypes::LogicalZone => Zone::class,
            ModelTypes::Action => Action::class,
            ModelTypes::Vlan => Vlan::class,
            ModelTypes::IpNetwork => IpNetwork::class,
            ModelTypes::DnsZone => DnsZone::class,
            ModelTypes::DnsRecord => DnsRecord::class,
            ModelTypes::Nic => Nic::class,
            ModelTypes::Technology => Technology::class,
            ModelTypes::TechnologyVersion => TechnologyVersion::class,
            ModelTypes::Criticality => Criticality::class,
            ModelTypes::SourceProvider => SourceProvider::class,
            ModelTypes::Regulation => Regulation::class,
            ModelTypes::Stage => Stage::class,
            ModelTypes::ProtectionGoal => ProtectionGoal::class,
            ModelTypes::ProtectionGoalLevel => ProtectionGoalLevel::class,
            ModelTypes::DataClassification => DataClassification::class,
            ModelTypes::AuditingTemplate => Template::class,
            ModelTypes::Policy => Policy::class,
            ModelTypes::AuditingRule => Rule::class,
            ModelTypes::Strategy => Strategy::class,
            ModelTypes::StrategyQuestion => Question::class,
            ModelTypes::Period => Period::class,
            ModelTypes::RelationshipType => RelationshipType::class,
            ModelTypes::Release => Release::class,
            ModelTypes::Metric => Metric::class,
        };
    }

    public static function toMap(): array
    {
        $r = [];

        foreach (static::cases() as $case) {
            $r[$case->value] = $case->modelClass();
        }


        return $r;
    }
}
