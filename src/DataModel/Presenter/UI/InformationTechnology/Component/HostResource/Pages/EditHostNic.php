<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Component\HostResource\Pages;

use Swark\DataModel\Presenter\UI\InformationTechnology\Component\HostResource;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\NicResource\Pages\EditChildNic;

class EditHostNic extends EditChildNic
{
    protected static string $resource = HostResource::class;
}
