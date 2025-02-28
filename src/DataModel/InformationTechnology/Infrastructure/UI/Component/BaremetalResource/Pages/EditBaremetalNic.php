<?php
namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\BaremetalResource\Pages;

use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\BaremetalResource;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\NicResource\Pages\EditChildNic;

class EditBaremetalNic extends EditChildNic {
    protected static string $resource = BaremetalResource::class;
}
