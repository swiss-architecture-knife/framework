<?php
namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\HostResource\Pages;

use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\HostResource;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\NicResource\Pages\CreateChildNic;


class CreateHostNic extends CreateChildNic {
    protected static string $resource = HostResource::class;
}
