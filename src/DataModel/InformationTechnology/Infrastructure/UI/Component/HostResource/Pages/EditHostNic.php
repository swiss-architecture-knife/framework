<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\HostResource\Pages;

use Swark\DataModel\InformationTechnology\Infrastructure\UI\Component\HostResource;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\NicResource\Pages\EditChildNic;

class EditHostNic extends EditChildNic
{
    protected static string $resource = HostResource::class;
}
