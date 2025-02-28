<?php
namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\HostResource\Pages;


use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\HostResource;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\NicResource\Pages\ManageChildNics;

class ManageHostNics extends ManageChildNics
{
    protected static string $resource = HostResource::class;
}
