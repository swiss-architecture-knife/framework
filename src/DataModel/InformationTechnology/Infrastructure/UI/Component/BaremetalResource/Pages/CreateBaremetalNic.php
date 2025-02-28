<?php
namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\BaremetalResource\Pages;

use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\BaremetalResource;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\NicResource\Pages\CreateChildNic;


class CreateBaremetalNic extends CreateChildNic {
    protected static string $resource = BaremetalResource::class;
}
