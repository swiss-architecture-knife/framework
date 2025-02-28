<?php
namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\BaremetalResource\Pages;


use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\BaremetalResource;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\NicResource\Pages\ManageChildNics;

class ManageBaremetalNics extends ManageChildNics
{
    protected static string $resource = BaremetalResource::class;
}
