<?php
namespace Swark\DataModel\Presenter\UI\InformationTechnology\Component\BaremetalResource\Pages;

use Swark\DataModel\Presenter\UI\InformationTechnology\Component\BaremetalResource;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\NicResource\Pages\EditChildNic;

class EditBaremetalNic extends EditChildNic {
    protected static string $resource = BaremetalResource::class;
}
