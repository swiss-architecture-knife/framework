<?php
namespace Swark\DataModel\Presenter\UI\InformationTechnology\Component\HostResource\Pages;


use Swark\DataModel\Presenter\UI\InformationTechnology\Component\HostResource;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\NicResource\Pages\ManageChildNics;

class ManageHostNics extends ManageChildNics
{
    protected static string $resource = HostResource::class;
}
