<?php
namespace Swark\Management\Architecture\Resources\Infrastructure\HostResource\Pages;

use Swark\Management\Architecture\Resources\Infrastructure\HostResource;
use Swark\Management\Architecture\Resources\Infrastructure\NicResource\Pages\CreateChildNic;


class CreateHostNic extends CreateChildNic {
    protected static string $resource = HostResource::class;
}
