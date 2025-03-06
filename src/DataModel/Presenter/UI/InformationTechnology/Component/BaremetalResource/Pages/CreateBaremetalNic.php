<?php
namespace Swark\DataModel\Presenter\UI\InformationTechnology\Component\BaremetalResource\Pages;

use Swark\DataModel\Presenter\UI\InformationTechnology\Component\BaremetalResource;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\NicResource\Pages\CreateChildNic;


class CreateBaremetalNic extends CreateChildNic {
    protected static string $resource = BaremetalResource::class;
}
