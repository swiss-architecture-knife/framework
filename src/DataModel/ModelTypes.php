<?php

namespace Swark\DataModel;

use Swark\DataModel\Auditing\Domain\Entity\Action;
use Swark\DataModel\Auditing\Domain\Entity\Finding;
use Swark\DataModel\Business\Domain\Entity\Actor;
use Swark\DataModel\Compliance\Domain\Entity\Control;
use Swark\DataModel\Deployment\Domain\Entity\ApplicationInstance;
use Swark\DataModel\Deployment\Domain\Entity\Deployment;
use Swark\DataModel\Governance\Domain\Entity\Strategy\Objective;
use Swark\DataModel\InformationTechnology\Domain\Entity\Cloud\Subscription;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Baremetal;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Cluster;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Host;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Resource;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\Runtime;
use Swark\DataModel\InformationTechnology\Domain\Entity\Component\System;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\DnsRecord;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\DnsZone;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\IpNetwork;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\Nic;
use Swark\DataModel\InformationTechnology\Domain\Entity\Network\Vlan;
use Swark\DataModel\InformationTechnology\Domain\Entity\Zone;
use Swark\DataModel\Meta\Domain\Entity\ResourceType;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Component;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Service;
use Swark\DataModel\SoftwareArchitecture\Domain\Entity\Software;

enum ModelTypes: string
{
    case Host = 'host';

    case Runtime = 'runtime';

    case Baremetal = 'baremetal';

    case Subscription = 'subscription';

    case Cluster = 'cluster';

    case ApplicationInstance = 'application_instance';

    case Resource = 'resource';

    case Deployment = 'deployment';

    case Software = 'software';

    case ResourceType = 'resource_type';

    case Actor = 'actor';

    case System = 'system';

    case Service = 'service';

    case Component = 'component';

    case Finding = 'finding';

    case Objective = 'objective';

    case Control = 'control';

    case LogicalZone = 'logical_zone';

    case Action = 'action';

    case Vlan = 'vlan';

    case IpNetwork = 'ip_network';

    case DnsZone = 'dns_zone';

    case DnsRecord = 'dns_record';
    case Nic = 'nic';

    public function modelClass(): string
    {
        return match ($this) {
            ModelTypes::Host => Host::class,
            ModelTypes::Runtime => Runtime::class,
            ModelTypes::Baremetal => Baremetal::class,
            ModelTypes::Subscription => Subscription::class,
            ModelTypes::Cluster => Cluster::class,
            ModelTypes::ApplicationInstance => ApplicationInstance::class,
            ModelTypes::Resource => Resource::class,
            ModelTypes::Deployment => Deployment::class,
            ModelTypes::Software => Software::class,
            ModelTypes::ResourceType => ResourceType::class,
            ModelTypes::Actor => Actor::class,
            ModelTypes::System => System::class,
            ModelTypes::Service => Service::class,
            ModelTypes::Component => Component::class,
            ModelTypes::Finding => Finding::class,
            ModelTypes::Objective => Objective::class,
            ModelTypes::Control => Control::class,
            ModelTypes::LogicalZone => Zone::class,
            ModelTypes::Action => Action::class,
            ModelTypes::Vlan => Vlan::class,
            ModelTypes::IpNetwork => IpNetwork::class,
            ModelTypes::DnsZone => DnsZone::class,
            ModelTypes::DnsRecord => DnsRecord::class,
            ModelTypes::Nic => Nic::class,
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
