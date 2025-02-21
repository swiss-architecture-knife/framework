<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\HostResource\Pages;

use Swark\Management\Architecture\Resources\Infrastructure\HostResource;
use Swark\Management\Architecture\Resources\Infrastructure\NicResource\Pages\EditChildNic;

class EditHostNic extends EditChildNic
{
    protected static string $resource = HostResource::class;
}
