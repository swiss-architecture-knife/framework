<?php
namespace Swark\DataModel\Presenter\UI\InformationTechnology\Component\BaremetalResource\Pages;


use Swark\DataModel\Presenter\UI\InformationTechnology\Component\BaremetalResource;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\NicResource\Pages\ManageChildNics;

class ManageBaremetalNics extends ManageChildNics
{
    protected static string $resource = BaremetalResource::class;
}
